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

    public function findUsers(
        int $limit,
        int $offset,
        ?string $search = null,
        ?int $roleId = null,
        string $sortBy = 'id',
        string $sortDirection = 'DESC',
    ): array {

        $sortDirection = strtoupper($sortDirection);

        if (!in_array($sortDirection, ['ASC', 'DESC'], true)) {
            $sortDirection = 'DESC';
        }

        $sql = "
            SELECT
                u.id,
                u.email,
                u.username,
                u.role_id,
                r.permission AS role_permission
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            WHERE 1 = 1
        ";

        $parameters = [];

        $this->buildUserFilters(
            $sql,
            $parameters,
            $search,
            $roleId
        );

        $sql .= "
            ORDER BY u.{$sortBy} {$sortDirection}
            LIMIT :limit
            OFFSET :offset
        ";

        $statement = $this->prepare($sql);

        foreach ($parameters as $key => $value) {
            $statement->bindValue(":{$key}", $value);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function countUsers(
        ?string $search = null,
        ?int $roleId = null
    ): int {

        $sql = "
            SELECT COUNT(*)
            FROM users u
            WHERE 1 = 1
        ";

        $parameters = [];

        $this->buildUserFilters(
            $sql,
            $parameters,
            $search,
            $roleId
        );

        $statement = $this->prepare($sql);

        $statement->execute($parameters);

        return (int) $statement->fetchColumn();
    }

    private function buildUserFilters(
        string &$sql,
        array &$parameters,
        ?string $search,
        ?int $roleId
    ): void {

        if ($search !== null && $search !== '') {
            $sql .= " AND (u.username LIKE :search_username OR u.email LIKE :search_email)";
            $parameters['search_username'] = '%' . $search . '%';
            $parameters['search_email'] = '%' . $search . '%';
        }

        if ($roleId !== null) {
            $sql .= " AND u.role_id = :role_id";
            $parameters['role_id'] = $roleId;
        }
    }

    public function findIdsByRole(int $roleId): array
    {
        $sql = "
            SELECT id
            FROM users
            WHERE role_id = :role_id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'role_id' => $roleId,
        ]);

        return array_map(
            'intval',
            $statement->fetchAll(PDO::FETCH_COLUMN)
        );
    }
}