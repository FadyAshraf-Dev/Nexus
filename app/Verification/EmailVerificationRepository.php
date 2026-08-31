<?php
declare(strict_types=1);

class EmailVerificationRepository extends Repository
{
    /**
     * Creates or replaces the single live verification row for a
     * user. user_id is UNIQUE on this table, so a resend (or a
     * second registration attempt) overwrites the previous
     * unclicked code rather than leaving stale rows behind.
     */
    public function createOrReplace(
        int $userId,
        string $selector,
        string $codeHash,
        DateTimeImmutable $expiresAt
    ): void {

        $sql = "
            INSERT INTO email_verifications
                (user_id, selector, code_hash, expires_at)
            VALUES
                (:user_id, :selector, :code_hash, :expires_at)
            ON DUPLICATE KEY UPDATE
                selector = VALUES(selector),
                code_hash = VALUES(code_hash),
                expires_at = VALUES(expires_at),
                created_at = CURRENT_TIMESTAMP()
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId,
            'selector' => $selector,
            'code_hash' => $codeHash,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findBySelector(string $selector): ?array
    {
        $sql = "
            SELECT *
            FROM email_verifications
            WHERE selector = :selector
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'selector' => $selector,
        ]);

        return $statement->fetch() ?: null;
    }

    public function deleteByUser(int $userId): bool
    {
        $sql = "
            DELETE FROM email_verifications
            WHERE user_id = :user_id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    public function deleteExpired(): bool
    {
        $sql = "
            DELETE FROM email_verifications
            WHERE expires_at < NOW()
        ";

        $statement = $this->prepare($sql);

        $statement->execute();

        return $statement->rowCount() > 0;
    }
}