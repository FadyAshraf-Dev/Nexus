<?php

declare(strict_types=1);

class PasswordResetRepository extends Repository
{
    /**
     * Creates or replaces the single live reset row for a user.
     * user_id is UNIQUE on this table (same pattern as
     * EmailVerificationRepository), so requesting a new reset link
     * invalidates any previous unclicked one automatically.
     */
    public function createOrReplace(
        int $userId,
        string $selector,
        string $tokenHash,
        DateTimeImmutable $expiresAt
    ): void {

        $sql = "
            INSERT INTO password_resets
                (user_id, selector, token_hash, expires_at)
            VALUES
                (:user_id, :selector, :token_hash, :expires_at)
            ON DUPLICATE KEY UPDATE
                selector = VALUES(selector),
                token_hash = VALUES(token_hash),
                expires_at = VALUES(expires_at),
                created_at = CURRENT_TIMESTAMP()
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId,
            'selector' => $selector,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findBySelector(string $selector): ?array
    {
        $sql = "
            SELECT *
            FROM password_resets
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
            DELETE FROM password_resets
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
            DELETE FROM password_resets
            WHERE expires_at < NOW()
        ";

        $statement = $this->prepare($sql);

        $statement->execute();

        return $statement->rowCount() > 0;
    }
}
