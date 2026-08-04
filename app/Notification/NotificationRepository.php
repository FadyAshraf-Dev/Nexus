<?php

declare(strict_types=1);

final class NotificationRepository extends Repository
{
    /**
     * Creates a single notification for one user.
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO notifications
            (
                user_id,
                content,
                url
            )
            VALUES
            (
                :user_id,
                :content,
                :url
            )
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $data['user_id'],
            'content' => $data['content'],
            'url'     => $data['url'] ?? null,
        ]);

        return $this->lastInsertId();
    }

    /**
     * Creates one notification per given user_id, all with the same
     * content/url. Used for "every admin gets notified" style triggers,
     * where each recipient needs their own row (and independent read
     * status) rather than one shared row.
     */
    public function createForUsers(
        array $userIds,
        string $content,
        ?string $url = null
    ): void {

        if (empty($userIds)) {
            return;
        }

        $sql = "
            INSERT INTO notifications
            (
                user_id,
                content,
                url
            )
            VALUES
            (
                :user_id,
                :content,
                :url
            )
        ";

        $statement = $this->prepare($sql);

        foreach ($userIds as $userId) {

            $statement->execute([
                'user_id' => $userId,
                'content' => $content,
                'url'     => $url,
            ]);

        }

    }

    /**
     * Fetches a user's notifications, most recent first, limited for
     * dropdown display (not a full paginated history).
     */
    public function findForUser(int $userId, int $limit = 10): array
    {
        $sql = "
            SELECT *
            FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit
        ";

        $statement = $this->prepare($sql);

        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Counts a user's unread notifications, for the bell icon badge.
     */
    public function countUnread(int $userId): int
    {
        $sql = "
            SELECT COUNT(*) AS unread_count
            FROM notifications
            WHERE user_id = :user_id
              AND is_read = 0
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId,
        ]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Marks all of a user's unread notifications as read in one query,
     * for the "mark all read on bell click" behavior.
     */
    public function markAllRead(int $userId): bool
    {
        $sql = "
            UPDATE notifications
            SET
                is_read = 1,
                read_at = NOW()
            WHERE user_id = :user_id
              AND is_read = 0
        ";

        $statement = $this->prepare($sql);

        return $statement->execute([
            'user_id' => $userId,
        ]);
    }
}
