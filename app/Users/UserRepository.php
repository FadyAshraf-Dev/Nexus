<?php

declare(strict_types=1);

final class UserRepository extends Repository
{
    public function create(array $userData): int
    {
        $sql = "
            INSERT INTO users (
                email,
                password,
                role_id
            )
            VALUES (
                :email,
                :password,
                :role_id
            )
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'email'    => $userData['email'],
            'password' => $userData['password'],
            'role_id'  => $userData['role_id'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(
        int $userId,
        array $userData
    ): void {

        $sql = "
            UPDATE users
            SET
                email = :email,
                password = :password,
                role_id = :role_id
            WHERE id = :id
        ";

        $userData['id'] = $userId;

        $statement = $this->prepare($sql);

        $statement->execute($userData);
    }

    public function delete(int $userId): bool
    {
        $sql = "
            DELETE FROM users
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $userId,
        ]);

        return $statement->rowCount() === 1;
    }

    public function findById(int $userId): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE id = :id
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $userId,
        ]);

        $user = $statement->fetch();

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetch();

        return $user ?: null;
    }
}