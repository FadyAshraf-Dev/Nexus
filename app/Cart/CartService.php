<?php

declare(strict_types=1);
final class CartService
{
    private PDO $pdo;

    private CartRepository $cartRepository;

    private ProductRepository $productRepository;

    private CookieCart $cookieCart;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->cartRepository =
            new CartRepository($pdo);

        $this->productRepository =
            new ProductRepository($pdo);

        $this->cookieCart =
            new CookieCart();
    }
    // --------------------------------------------------
    // Public API
    // --------------------------------------------------

    /**
     * Adds a product to the current user's cart.
     *
     * Guests:
     *  - Stored inside browser cookies.
     *
     * Authenticated users:
     *  - Stored inside the database.
     */
    public function addItem(
        int $productId,
        int $quantity = 1
    ): void {
        if ($quantity < 1) {
            throw new InvalidArgumentException(
                'Quantity must be at least 1.'
            );
        }

        $product = $this->productRepository
            ->findById($productId);

        if ($product === null) {
            throw new RuntimeException(
                'Product not found.'
            );
        }

        if (
            (int) $product['stock_quantity'] < 1
        ) {
            throw new RuntimeException(
                'Product is out of stock.'
            );
        }

        $quantity = min(
            $quantity,
            (int) $product['stock_quantity']
        );

        if ($this->isGuest()) {

            $this->addCookieItem(
                $productId,
                $quantity,
                (int) $product['stock_quantity']

            );

            return;
        }

        $this->addDatabaseItem(
            $productId,
            $quantity,
            (int) $product['stock_quantity']
        );
    }
    /**
     * Updates the quantity of an existing cart item.
     *
     * Quantity is automatically capped by
     * the product's available stock.
     */
    public function updateQuantity(
        int $productId,
        int $quantity
    ): void {

        if ($quantity < 1) {
            throw new InvalidArgumentException(
                'Quantity must be at least 1.'
            );
        }

        $product = $this->productRepository->findById(
            $productId
        );

        if ($product === null) {
            throw new RuntimeException(
                'Product not found.'
            );
        }

        $quantity = min(
            $quantity,
            (int) $product['stock_quantity']
        );

        if ($this->isGuest()) {

            $this->updateCookieItemQuantity(
                $productId,
                $quantity
            );

            return;
        }

        $cartId = $this->getOrCreateCartId();

        $item = $this->cartRepository->findItem(
            $cartId,
            $productId
        );

        if ($item === null) {
            throw new RuntimeException(
                'Cart item not found.'
            );
        }

        $this->cartRepository->updateItemQuantity(
            (int) $item['id'],
            $quantity
        );
    }
    /**
     * Removes a single product from the cart.
     */
    public function removeItem(
        int $productId
    ): void {

        if ($this->isGuest()) {

            $this->removeCookieItem($productId);

            return;
        }

        $cartId = $this->getOrCreateCartId();

        $this->cartRepository->removeItem(
            $cartId,
            $productId
        );
    }
    /**
     * Removes every item from the cart.
     */
    public function clear(): void
    {
        if ($this->isGuest()) {

            $this->cookieCart->delete();

            return;
        }

        $this->cartRepository->clearCart(
            $this->getOrCreateCartId()
        );
    }
    /**
     * Returns the current cart.
     *
     * Guests receive the cookie cart.
     * Logged-in users receive the database cart.
     */
    public function getCart(): array
    {
        if ($this->isGuest()) {
            return $this->cookieCart->read();
        }

        $cartId = $this->getOrCreateCartId();
        return $this->cartRepository
            ->findItems($cartId);
    }
    /**
     * Returns the total quantity of items
     * currently inside the cart.
     */
    public function getItemCount(): int
    {
        $count = 0;

        foreach (
            $this->getCart() as $item
        ) {

            $count +=
                (int) $item['quantity'];

        }

        return $count;
    }

    /**
     * Merges the anonymous cookie cart into the
     * authenticated user's database cart after login.
     *
     * Cookie quantities are merged with existing
     * database quantities and capped by stock.
     */
    public function mergeCookieIntoDatabase(): void
    {
        if (
            $this->isGuest()
            || !$this->cookieCart->exists()
        ) {
            return;
        }

        foreach (
            $this->cookieCart->read() as $item
        ) {

            $this->addItem(
                (int) $item['product_id'],
                (int) $item['quantity']
            );
        }

        $this->cookieCart->delete();
    }






    // --------------------------------------------------
    // Private Helpers
    // --------------------------------------------------
    /**
     * Adds a product to the authenticated user's
     * database cart.
     */
    private function addDatabaseItem(
        int $productId,
        int $quantity,
        int $stock

    ): void {

        $cartId = $this->getOrCreateCartId();

        $item = $this->cartRepository->findItem(
            $cartId,
            $productId
        );

        if ($item === null) {

            $this->cartRepository->createItem(
                $cartId,
                $productId,
                $quantity
            );

            return;
        }

        $newQuantity = min(
            (int) $item['quantity'] + $quantity,
            $stock
        );
        $this->cartRepository->updateItemQuantity(
            (int) $item['id'],
            $newQuantity
        );
    }
    /**
     * Adds a product to the anonymous cookie cart.
     */
    private function addCookieItem(
        int $productId,
        int $quantity,
        int $stock
    ): void {

        $cart = $this->cookieCart->read();

        foreach ($cart as &$item) {

            if ($item['product_id'] !== $productId) {
                continue;
            }

            $item['quantity'] = min(
                $item['quantity'] + $quantity,
                $stock
            );

            $this->cookieCart->write($cart);

            return;
        }

        $cart[] = [
            'product_id' => $productId,
            'quantity' => min($quantity, $stock),
        ];

        $this->cookieCart->write($cart);
    }
    /**
     * Updates the quantity of a cookie cart item.
     */
    private function updateCookieItemQuantity(
        int $productId,
        int $quantity
    ): void {

        $cart = $this->cookieCart->read();

        foreach ($cart as &$item) {

            if ($item['product_id'] !== $productId) {
                continue;
            }

            $item['quantity'] = $quantity;

            break;
        }

        $this->cookieCart->write($cart);
    }
    /**
     * Removes a product from the cookie cart.
     */
    private function removeCookieItem(
        int $productId
    ): void {

        $cart = $this->cookieCart->read();

        $cart = array_values(
            array_filter(
                $cart,
                static fn(array $item): bool =>
                $item['product_id'] !== $productId
            )
        );

        if ($cart === []) {

            $this->cookieCart->delete();

            return;
        }

        $this->cookieCart->write($cart);
    }
    /**
     * Returns the current user's cart ID,
     * creating a new cart if necessary.
     */
    private function getOrCreateCartId(): int
    {
        $cart = $this->cartRepository->findCartByUserId(
            Session::id()
        );

        if ($cart !== null) {
            return (int) $cart["id"];
        }

        return $this->cartRepository->createCart(
            Session::id()
        );
    }
    /**
     * Determines whether the current visitor
     * is authenticated.
     */
    private function isGuest(): bool
    {
        return Session::guest();
    }

}