<?php
declare(strict_types=1);

class TokenRepository extends Repository
{
    public function create(
        int $userId,
        string $selector,
        string $hashedToken,
        DateTimeImmutable $expiresAt
    ): int {
        try{
        $sql = "INSERT INTO tokens
        (
            user_id,
            selector,
            token_hash,
            expires_at
        )
        VALUES
        (
            :user_id,
            :selector,
            :token_hash,
            :expires_at
        )";
        $statement = $this->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
            'selector' => $selector,
            'token_hash' => $hashedToken,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
        return (int) $this->pdo->lastInsertId();

        }
        catch(Throwable $e){
            die($e->getMessage());
        }
    }
    public function findBySelector(
        string $selector
    ): ?array {
        $sql = "SELECT *
        FROM tokens
        WHERE selector = :selector
        LIMIT 1";
        $statement = $this->prepare($sql);
        $statement->execute([
            "selector" => $selector
        ]);
        return $statement->fetch() ?: null;

    }
    public function updateToken(
        int $id,
        string $selector,
        string $hashedToken,
        DateTimeInterface $expiresAt
    ): void {
        $sql = "UPDATE tokens
        SET
            selector = :selector,
            token_hash = :token_hash,
            expires_at = :expires_at
        WHERE id = :id";
        $statement = $this->prepare($sql);
        $statement->execute([
            'selector' => $selector,
            'token_hash' => $hashedToken,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'id' => $id,
        ]);
        if ($statement->rowCount() !== 1) {
            throw new RuntimeException(
                'Unable to rotate remember token.'
            );
        }
    }
    public function delete(
        int $id
    ): bool {
        $sql = "DELETE FROM tokens 
        WHERE id = :id";
        $statement = $this->prepare($sql);
        $statement->execute([
            'id' => $id,
        ]);

        return $statement->rowCount() === 1;

    }
    public function deleteByUser(
        int $userId
    ): bool {
        $sql = "DELETE FROM tokens 
        WHERE user_id = :user_id";
        $statement = $this->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;

    }
    public function deleteExpired(): bool
    {
        $sql = "DELETE
        FROM tokens
        WHERE expires_at < NOW()";
        $statement = $this->prepare($sql);
        $statement->execute();

        return $statement->rowCount() > 0;

    }

}