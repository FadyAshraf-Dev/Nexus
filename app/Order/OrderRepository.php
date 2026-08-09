<?php

declare(strict_types=1);


class OrderRepository
{
    public function __construct(private readonly PDO $pdo) {}

    public function createOrder(array $order): int
    {
        $statement = $this->pdo->prepare(
            "
            INSERT INTO orders
            (
                user_id,
                status,
                address,
                phone,
                coupon_id,
                shipping_price,
                discount_amount,
                total_price
            )
            VALUES
            (
                :user_id,
                :status,
                :address,
                :phone,
                :coupon_id,
                :shipping_price,
                :discount_amount,
                :total_price
            )
            "
        );

        $statement->execute([

            "user_id" => $order["user_id"],
            "status" => $order["status"],
            "address" => $order["address"],
            "phone" => $order["phone"],
            "coupon_id" => $order["coupon_id"] ?? null,
            "shipping_price" => $order["shipping_price"],
            "discount_amount" => $order["discount_amount"] ?? 0.00,
            "total_price" => $order["total_price"]

        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findOrderById(int $orderId): ?array
    {
        $statement = $this->pdo->prepare(
            "
            SELECT *
            FROM orders
            WHERE id = :id
            LIMIT 1
            "
        );

        $statement->execute([
            "id" => $orderId
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findUserOrders(int $userId): array
    {
        $statement = $this->pdo->prepare(
            "
            SELECT *
            FROM orders
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            "
        );

        $statement->execute([
            "user_id" => $userId
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches order_items (joined with product name/image) for multiple
     * orders in a single query, to avoid N+1 when rendering an order list
     * that shows item thumbnails per order.
     *
     * Returns a flat array; each row includes order_id so the caller can
     * group them.
     */
    public function findItemsForOrders(array $orderIds): array
    {
        if (empty($orderIds)) {
            return [];
        }

        $placeholders = implode(
            ',',
            array_fill(0, count($orderIds), '?')
        );

        $statement = $this->pdo->prepare(
            "
            SELECT
                oi.id,
                oi.order_id,
                oi.product_id,
                oi.quantity,
                oi.price,

                p.product_name,
                p.slug,

                (
                    SELECT image_path
                    FROM product_images
                    WHERE product_id = p.id
                    ORDER BY sort_order
                    LIMIT 1
                ) AS image_path

            FROM order_items oi

            LEFT JOIN products p
                ON p.id = oi.product_id

            WHERE oi.order_id IN ({$placeholders})

            ORDER BY oi.order_id ASC, oi.id ASC
            "
        );

        $statement->execute($orderIds);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus(
        int $orderId,
        string $status
    ): bool {

        $statement = $this->pdo->prepare(
            "
            UPDATE orders
            SET status = :status
            WHERE id = :id
            "
        );

        $statement->execute([

            "status" => $status,
            "id" => $orderId

        ]);

        return $statement->rowCount() === 1;
    }

    /* =====================================================
       Order Items
    ===================================================== */

    public function createOrderItems(array $items): void
    {
        foreach ($items as $item) {

            $this->createOrderItem($item);

        }
    }

    private function createOrderItem(array $item): bool
    {
        $statement = $this->pdo->prepare(
            "
            INSERT INTO order_items
            (
                order_id,
                product_id,
                quantity,
                price
            )
            VALUES
            (
                :order_id,
                :product_id,
                :quantity,
                :price
            )
            "
        );

        return $statement->execute([

            "order_id" => $item["order_id"],
            "product_id" => $item["product_id"],
            "quantity" => $item["quantity"],
            "price" => $item["price"]

        ]);
    }

    public function findOrderItems(int $orderId): array
    {
        $statement = $this->pdo->prepare(
            "
            SELECT
                oi.id,
                oi.product_id,
                oi.quantity,
                oi.price,

                p.product_name,
                p.slug,

                (
                    SELECT image_path
                    FROM product_images
                    WHERE product_id = p.id
                    ORDER BY sort_order
                    LIMIT 1
                ) AS image_path

            FROM order_items oi

            LEFT JOIN products p
                ON p.id = oi.product_id

            WHERE oi.order_id = :order_id

            ORDER BY oi.id ASC
            "
        );

        $statement->execute([
            "order_id" => $orderId
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =====================================================
       Combined Queries
    ===================================================== */

    public function findOrderWithItems(int $orderId): ?array
    {
        $order = $this->findOrderById($orderId);

        if (!$order) {
            return null;
        }

        $order["items"] =
            $this->findOrderItems($orderId);

        return $order;
    }

    /**
     * Finds a delivered order_item for this user/product that hasn't
     * already been reviewed, if one exists.
     *
     * This is the core "is this user eligible to review this product"
     * check - a user can review a product if and only if they have at
     * least one delivered order containing it, and haven't already used
     * that specific order_item for a review (the reviews table's unique
     * constraint on order_item_id is the hard backstop for this; this
     * query is what lets the API give a clean "not eligible" response
     * instead of hitting a raw constraint violation).
     *
     * Returns the order_item id if eligible, null otherwise.
     */
    public function findEligibleOrderItem(
        int $userId,
        int $productId
    ): ?int {

        $statement = $this->pdo->prepare(
            "
            SELECT oi.id

            FROM order_items oi

            INNER JOIN orders o
                ON o.id = oi.order_id

            LEFT JOIN reviews r
                ON r.order_item_id = oi.id
                AND r.deleted_at IS NULL

            WHERE o.user_id = :user_id
              AND oi.product_id = :product_id
              AND o.status = 'delivered'
              AND r.id IS NULL

            ORDER BY oi.id ASC

            LIMIT 1
            "
        );

        $statement->execute([
            "user_id" => $userId,
            "product_id" => $productId,
        ]);

        $orderItemId = $statement->fetchColumn();

        return $orderItemId !== false ? (int) $orderItemId : null;

    }

    /**
     * Counts orders matching admin listing filters. Unlike
     * findUserOrders (scoped to one customer's own order history),
     * this is admin-wide - every order, filterable by status.
     */
    public function countForAdmin(?string $status = null): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM orders
            WHERE 1 = 1
        ";

        $parameters = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status";
            $parameters['status'] = $status;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn();
    }

    /**
     * Paginated, filterable order listing for admin management, joined
     * to users for customer display context (email/username).
     */
    public function findForAdmin(
        int $limit,
        int $offset,
        ?string $status = null
    ): array {

        $sql = "
            SELECT
                o.id,
                o.user_id,
                o.status,
                o.address,
                o.phone,
                o.total_price,
                o.created_at,
                COALESCE(u.username, u.email) AS customer_name

            FROM orders o

            INNER JOIN users u ON u.id = o.user_id

            WHERE 1 = 1
        ";

        $parameters = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND o.status = :status";
            $parameters['status'] = $status;
        }

        $sql .= "
            ORDER BY o.created_at DESC
            LIMIT :limit
            OFFSET :offset
        ";

        $statement = $this->pdo->prepare($sql);

        foreach ($parameters as $key => $value) {
            $statement->bindValue(":{$key}", $value);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}