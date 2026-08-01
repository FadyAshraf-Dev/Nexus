<?php

declare(strict_types=1);

class CouponRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * Create a new coupon.
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO coupons (
                code,
                type,
                value,
                minimum_order,
                maximum_discount,
                usage_limit,
                used_count,
                starts_at,
                expires_at,
                status
            )
            VALUES (
                :code,
                :type,
                :value,
                :minimum_order,
                :maximum_discount,
                :usage_limit,
                :used_count,
                :starts_at,
                :expires_at,
                :status
            )
        ";

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'code'             => $data['code'],
            'type'             => $data['type'],
            'value'            => $data['value'],
            'minimum_order'    => $data['minimum_order'],
            'maximum_discount' => $data['maximum_discount'],
            'usage_limit'      => $data['usage_limit'],
            'used_count'       => $data['used_count'] ?? 0,
            'starts_at'        => $data['starts_at'],
            'expires_at'       => $data['expires_at'],
            'status'           => $data['status'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Find coupon by ID.
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM coupons
            WHERE id = :id
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $id,
        ]);

        $coupon = $statement->fetch(PDO::FETCH_ASSOC);

        return $coupon ?: null;
    }

    /**
     * Find coupon by code.
     */
    public function findByCode(string $code): ?array
    {
        $sql = "
            SELECT *
            FROM coupons
            WHERE code = :code
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'code' => $code,
        ]);

        $coupon = $statement->fetch(PDO::FETCH_ASSOC);

        return $coupon ?: null;
    }

    /**
     * Update coupon.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE coupons
            SET
                code = :code,
                type = :type,
                value = :value,
                minimum_order = :minimum_order,
                maximum_discount = :maximum_discount,
                usage_limit = :usage_limit,
                starts_at = :starts_at,
                expires_at = :expires_at,
                status = :status
            WHERE id = :id
              AND deleted_at IS NULL
        ";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'id'               => $id,
            'code'             => $data['code'],
            'type'             => $data['type'],
            'value'            => $data['value'],
            'minimum_order'    => $data['minimum_order'],
            'maximum_discount' => $data['maximum_discount'],
            'usage_limit'      => $data['usage_limit'],
            'starts_at'        => $data['starts_at'],
            'expires_at'       => $data['expires_at'],
            'status'           => $data['status'],
        ]);
    }

    /**
     * Soft delete coupon.
     */
    public function delete(int $id): bool
    {
        $sql = "
            UPDATE coupons
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
              AND deleted_at IS NULL
        ";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'id' => $id,
        ]);
    }

    /**
     * Increment successful usage count.
     */
    public function incrementUsage(int $id): bool
    {
        $sql = "
            UPDATE coupons
            SET used_count = used_count + 1
            WHERE id = :id
              AND deleted_at IS NULL
        ";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'id' => $id,
        ]);
    }

    /**
     * Retrieve all active (non-deleted) coupons.
     */
    public function findAll(): array
    {
        $sql = "
            SELECT *
            FROM coupons
            WHERE deleted_at IS NULL
            ORDER BY created_at DESC
        ";

        $statement = $this->pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}