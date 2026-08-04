<?php

declare(strict_types=1);

final class OrderService
{
    public const DEFAULT_SHIPPING_PRICE = 50.00;
    private CartRepository $cartRepository;
    private ProductRepository $productRepository;
    private OrderRepository $orderRepository;
    private CouponService $couponService;
    private ProductService $productService;
    private NotificationService $notificationService;

    public function __construct(
        private readonly PDO $pdo,
    ) {
        $this->cartRepository =
            new CartRepository($pdo);

        $this->productRepository =
            new ProductRepository($pdo);
        $this->orderRepository =
            new OrderRepository($pdo);

        // Same PDO instance, so applyCoupon()/incrementUsage() below
        // run inside this method's transaction, not a separate one.
        $this->couponService =
            new CouponService($pdo);

        // Same reasoning - stock decrements must be inside this
        // transaction, so a rollback also reverts them.
        $this->productService =
            new ProductService($pdo);

        $this->notificationService =
            new NotificationService($pdo);

    }

    /**
     * Creates a complete order.
     *
     * Validation is assumed to be already done.
     */
    public function placeOrder(
        int $userId,
        string $address,
        string $phone,
        ?string $couponCode=null,
    ): int {

        $cart = $this->cartRepository->findCartByUserId($userId);

        if ($cart === null) {
            throw new RuntimeException("Cart not found.");
        }

        $cartItems = $this->cartRepository->findItems(
            (int) $cart["id"]
        );

        if (empty($cartItems)) {
            throw new RuntimeException("Cart is empty.");
        }

        $this->pdo->beginTransaction();

        try {

            $orderItems = [];

            $subtotal = 0.0;

            foreach ($cartItems as $cartItem) {

                /*
                 * Product disappeared between reading
                 * the cart and checkout.
                 */

                if ($cartItem["status"] !== "active") {

                    throw new RuntimeException(
                        "{$cartItem["product_name"]} is no longer available."
                    );

                }

                /*
                 * Stock changed.
                 */

                if (
                    (int) $cartItem["quantity"] >
                    (int) $cartItem["stock_quantity"]
                ) {

                    throw new RuntimeException(
                        "{$cartItem["product_name"]} does not have enough stock."
                    );

                }

                /*
                 * Snapshot current selling price.
                 * (Discount calculation can later
                 * live in a dedicated PricingService.)
                 */

                $price = (float) $cartItem["selling_price"];

                $quantity = (int) $cartItem["quantity"];

                $subtotal += $price * $quantity;

                $orderItems[] = [

                    "product_id" => (int) $cartItem["product_id"],

                    "quantity" => $quantity,

                    "price" => $price

                ];

            }

            $shippingPrice = self::DEFAULT_SHIPPING_PRICE;

            $couponId = null;

            $discountAmount = 0.0;

            if ($couponCode !== null && trim($couponCode) !== '') {

                $result = $this->couponService->applyCoupon(
                    $couponCode,
                    $subtotal
                );

                $couponId = (int) $result["coupon"]["id"];

                $discountAmount = (float) $result["discount"];

            }

            $totalPrice = max(
                0.0,
                $subtotal + $shippingPrice - $discountAmount
            );

            $orderId = $this->orderRepository->createOrder([

                "user_id" => $userId,

                "status" => "pending",

                "address" => $address,

                "phone" => $phone,

                "coupon_id" => $couponId,

                "shipping_price" => $shippingPrice,

                "discount_amount" => $discountAmount,

                "total_price" => $totalPrice

            ]);

            foreach ($orderItems as &$item) {

                $item["order_id"] = $orderId;

            }

            unset($item);

            $this->orderRepository->createOrderItems(
                $orderItems
            );

            $lowStockCrossings = [];

            foreach ($orderItems as $item) {

                $fulfillment = $this->productService->fulfillOrderItem(
                    $item["product_id"],
                    $item["quantity"]
                );

                if (!$fulfillment["success"]) {
                    // Stock changed between the earlier read-based check
                    // and this atomic decrement (e.g. a concurrent order
                    // beat this one to the last units). Fail the whole
                    // order rather than sell something we don't have.
                    throw new RuntimeException(
                        "{$fulfillment['product']['product_name']} no longer has enough stock."
                    );
                }

                if ($fulfillment["crossed_threshold"]) {
                    $lowStockCrossings[] = $fulfillment["product"];
                }

            }

            if ($couponId !== null) {
                $this->couponService->incrementUsage($couponId);
            }

            $this->cartRepository->clearCart(
                (int) $cart["id"]
            );

            $this->pdo->commit();

            // Notifications are best-effort and intentionally happen
            // AFTER commit(): if notification insertion failed inside
            // the transaction, it would roll back a successfully placed
            // order over what is genuinely a non-critical side effect.
            $this->sendOrderNotifications($orderId, $userId, $lowStockCrossings);

            return $orderId;

        } catch (Throwable $exception) {

            $this->pdo->rollBack();

            throw $exception;

        }

    }

    /**
     * Sends order-related notifications. Called AFTER the order
     * transaction has already committed - failures here are logged,
     * never thrown, since a notification problem must not look like a
     * failed order to the customer who already successfully checked out.
     *
     * $userId (the purchaser) is accepted for symmetry/future use (e.g.
     * a "your order shipped" notification back to the buyer) even
     * though today's triggers only notify admins/vendors, not the buyer.
     */
    private function sendOrderNotifications(
        int $orderId,
        int $userId,
        array $lowStockCrossings
    ): void {

        try {

            $this->notificationService->notifyAdminsOfNewOrder($orderId);

            foreach ($lowStockCrossings as $product) {
                $this->notificationService->notifyVendorOfLowStock($product);
            }

        } catch (Throwable $exception) {

            error_log((string) $exception);

        }

    }

    public function findOrder(
        int $orderId
    ): ?array {

        return $this->orderRepository
            ->findOrderWithItems($orderId);

    }

    public function findUserOrders(
        int $userId
    ): array {

        $orders = $this->orderRepository
            ->findUserOrders($userId);

        if (empty($orders)) {
            return [];
        }

        $orderIds = array_map(
            static fn(array $order): int => (int) $order["id"],
            $orders
        );

        $items = $this->orderRepository
            ->findItemsForOrders($orderIds);

        $itemsByOrder = [];

        foreach ($items as $item) {
            $itemsByOrder[(int) $item["order_id"]][] = $item;
        }

        foreach ($orders as &$order) {
            $order["items"] = $itemsByOrder[(int) $order["id"]] ?? [];
        }

        unset($order);

        return $orders;

    }

    public function updateStatus(
        int $orderId,
        string $status
    ): bool {

        return $this->orderRepository
            ->updateOrderStatus(
                $orderId,
                $status
            );

    }
}