<?php

declare(strict_types=1);

use PDO;

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
                shipping_price,
                total_price
            )
            VALUES
            (
                :user_id,
                :status,
                :address,
                :phone,
                :shipping_price,
                :total_price
            )
            "
        );

        $statement->execute([

            "user_id" => $order["user_id"],
            "status" => $order["status"],
            "address" => $order["address"],
            "phone" => $order["phone"],
            "shipping_price" => $order["shipping_price"],
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

        return $statement->execute([

            "status" => $status,
            "id" => $orderId

        ]);
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
            SELECT *
            FROM order_items
            WHERE order_id = :order_id
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
}