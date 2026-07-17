<?php

declare(strict_types=1);

final class CartRepository extends Repository
{
    public function create(int $userId): int
    {
        $sql = "
            INSERT INTO carts
            (
                user_id
            )
            VALUES
            (
                :user_id
            )
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId
        ]);

        return $this->lastInsertId();
    }

    public function findByUserId(int $userId): ?array
    {
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

    public function findItems(int $cartId): array
    {
        $sql = "
            SELECT *
            FROM cart_items
            WHERE cart_id = :cart_id
            ORDER BY created_at ASC
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

    public function addItem(
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

    public function updateQuantity(
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

    public function clear(int $cartId): void
    {
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