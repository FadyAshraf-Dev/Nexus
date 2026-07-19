<?php

declare(strict_types=1);

final class CartRepository extends Repository
{
    public function createCart(int $userId): int
    {
        $sql = "
            INSERT INTO carts (user_id)
            VALUES (:user_id)
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId
        ]);

        return $this->lastInsertId();
    }

    public function findCartByUserId(
        int $userId
    ): ?array {

        $sql = "
            SELECT *
            FROM carts
            WHERE user_id = :user_id
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId
        ]);

        $cart = $statement->fetch();

        return $cart ?: null;
    }

    public function findCartIdByUserId(
        int $userId
    ): ?int {

        $sql = "
            SELECT id
            FROM carts
            WHERE user_id = :user_id
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId
        ]);

        $id = $statement->fetchColumn();

        return $id !== false
            ? (int) $id
            : null;
    }

    public function getOrCreateCartId(
        int $userId
    ): int {

        $cartId = $this->findCartIdByUserId($userId);

        if ($cartId !== null) {
            return $cartId;
        }

        return $this->createCart($userId);
    }

    public function findItems(
        int $cartId
    ): array {

        $sql = "
    SELECT
        ci.id,
        ci.product_id,
        ci.quantity,

        p.product_name,
        p.slug,
        p.selling_price,
        p.discount_type,
        p.discount_value,
        p.stock_quantity,
        p.status,

        (
            SELECT image_path
            FROM product_images
            WHERE product_id = p.id
            ORDER BY sort_order
            LIMIT 1
        ) AS image_path

    FROM cart_items ci

    INNER JOIN products p
        ON p.id = ci.product_id

    WHERE
        ci.cart_id = :cart_id
        AND p.deleted_at IS NULL

    ORDER BY ci.created_at ASC
";
        $statement = $this->prepare($sql);

        $statement->execute([
            'cart_id' => $cartId
        ]);

        return $statement->fetchAll();
    }

    public function findItem(
        int $cartId,
        int $productId
    ): ?array {

        $sql = "
            SELECT *
            FROM cart_items
            WHERE
                cart_id = :cart_id
                AND product_id = :product_id
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'cart_id' => $cartId,
            'product_id' => $productId
        ]);

        $item = $statement->fetch();

        return $item ?: null;
    }

    public function createItem(
        int $cartId,
        int $productId,
        int $quantity
    ): int {

        $sql = "
            INSERT INTO cart_items
            (
                cart_id,
                product_id,
                quantity
            )
            VALUES
            (
                :cart_id,
                :product_id,
                :quantity
            )
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity
        ]);

        return $this->lastInsertId();
    }

    public function updateItemQuantity(
        int $cartItemId,
        int $quantity
    ): void {

        $sql = "
            UPDATE cart_items
            SET quantity = :quantity
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $cartItemId,
            'quantity' => $quantity
        ]);
    }

    public function increaseItemQuantity(
        int $cartItemId,
        int $quantity
    ): void {

        $sql = "
            UPDATE cart_items
            SET quantity = quantity + :quantity
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $cartItemId,
            'quantity' => $quantity
        ]);
    }

    public function removeItem(
        int $cartId,
        int $productId
    ): bool {

        $sql = "
            DELETE
            FROM cart_items
            WHERE
                cart_id = :cart_id
                AND product_id = :product_id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'cart_id' => $cartId,
            'product_id' => $productId
        ]);

        return $statement->rowCount() === 1;
    }

    public function clearCart(
        int $cartId
    ): void {

        $sql = "
            DELETE
            FROM cart_items
            WHERE cart_id = :cart_id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'cart_id' => $cartId
        ]);
    }
}