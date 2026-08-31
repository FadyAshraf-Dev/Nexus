<?php

declare(strict_types=1);

final class VendorRequestRepository extends Repository
{
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO vendor_requests
                (user_id, store_name, description, phone)
            VALUES
                (:user_id, :store_name, :description, :phone)
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $data['user_id'],
            'store_name' => $data['store_name'],
            'description' => $data['description'],
            'phone' => $data['phone'],
        ]);

        return $this->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM vendor_requests
            WHERE id = :id
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $id,
        ]);

        return $statement->fetch() ?: null;
    }

    /**
     * The single live pending request for a user, if any - used to
     * block a second submission while one is already awaiting
     * review. Not a DB-level UNIQUE constraint, since a user can
     * have multiple past (approved/rejected) rows over time; only
     * "pending" is meant to be singular per user at once.
     */
    public function findPendingForUser(int $userId): ?array
    {
        $sql = "
            SELECT *
            FROM vendor_requests
            WHERE user_id = :user_id
              AND status = 'pending'
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->fetch() ?: null;
    }

    public function findAll(?string $status = null): array
    {
        $sql = "
            SELECT
                vr.*,
                u.username,
                u.email
            FROM vendor_requests vr
            INNER JOIN users u ON u.id = vr.user_id
        ";

        $parameters = [];

        if ($status !== null) {
            $sql .= " WHERE vr.status = :status";
            $parameters['status'] = $status;
        }

        $sql .= " ORDER BY vr.created_at DESC";

        $statement = $this->prepare($sql);

        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function approve(int $id, int $reviewerId): void
    {
        $sql = "
            UPDATE vendor_requests
            SET
                status = 'approved',
                reviewed_by = :reviewer_id,
                reviewed_at = NOW()
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'reviewer_id' => $reviewerId,
            'id' => $id,
        ]);
    }

    public function reject(
        int $id,
        int $reviewerId,
        string $reason
    ): void {

        $sql = "
            UPDATE vendor_requests
            SET
                status = 'rejected',
                reviewed_by = :reviewer_id,
                reviewed_at = NOW(),
                rejection_reason = :reason
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'reviewer_id' => $reviewerId,
            'reason' => $reason,
            'id' => $id,
        ]);
    }
}
