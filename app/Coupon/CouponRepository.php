<?php

declare(strict_types=1);

class CouponRepository extends Repository
{
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

    /**
     * Counts coupons for the admin listing, matching the same filters
     * findForListing applies - kept in sync so the count and the
     * actual page always agree on what matches.
     */
    public function countForListing(?string $status = null): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM coupons
            WHERE deleted_at IS NULL
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
     * Paginated, filterable coupon listing for admin management.
     * Soft-deleted coupons are never shown here (unlike reviews'
     * moderation table, which intentionally shows hidden ones too, so
     * they can be restored) - there is no "restore a deleted coupon"
     * feature, so excluding them entirely is correct here.
     */
    public function findForListing(
        int $limit,
        int $offset,
        ?string $status = null
    ): array {

        $sql = "
            SELECT *
            FROM coupons
            WHERE deleted_at IS NULL
        ";

        $parameters = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status";
            $parameters['status'] = $status;
        }

        $sql .= "
            ORDER BY created_at DESC
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

    /**
     * Toggles a coupon between active/inactive - the admin listing's
     * quick status action, separate from the full update() (which
     * requires every field, same reasoning as ProductRepository's
     * narrow updateRating vs its full update()).
     */
    public function updateStatus(int $id, string $status): bool
    {
        $sql = "
            UPDATE coupons
            SET status = :status
            WHERE id = :id
              AND deleted_at IS NULL
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $id,
            'status' => $status,
        ]);

        return $statement->rowCount() === 1;
    }
}