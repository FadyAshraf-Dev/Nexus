<?php

declare(strict_types=1);

final class UserRepository extends Repository
{
    public function create(array $userData): int
    {
        $sql = "
            INSERT INTO users (
                email,
                username,
                password,
                role_id
            )
            VALUES (
                :email,
                :username,
                :password,
                :role_id
            )
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'email'    => $userData['email'],
            'username' => $userData['username'],
            'password' => $userData['password'],
            'role_id'  => $userData['role_id'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function markEmailVerified(int $userId): void
    {
        $sql = "
            UPDATE users
            SET email_verified_at = CURRENT_TIMESTAMP()
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $userId,
        ]);
    }

    public function findByUsername(string $username): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE username = :username
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'username' => $username,
        ]);

        $user = $statement->fetch();

        return $user ?: null;
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

    /**
     * Partial update for the profile-edit flow: only email and
     * username. Deliberately separate from update() (which is the
     * admin user-management form and always writes email +
     * password + role_id together) so this can't accidentally
     * clobber the password hash or role.
     *
     * Setting email_verified_at back to NULL only when the email
     * actually changed keeps a "resave without changing anything"
     * submit from silently de-verifying the account.
     */
    public function updateProfile(
        int $userId,
        string $email,
        string $username,
        bool $emailChanged
    ): void {

        $sql = "
            UPDATE users
            SET
                email = :email,
                username = :username
                " . ($emailChanged ? ", email_verified_at = NULL" : "") . "
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'email' => $email,
            'username' => $username,
            'id' => $userId,
        ]);
    }

    /**
     * Partial update for the change-password / reset-password
     * flows: only the password hash. Separate from update() for
     * the same reason as updateProfile() above.
     */
    public function updatePassword(
        int $userId,
        string $passwordHash
    ): void {

        $sql = "
            UPDATE users
            SET password = :password
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'password' => $passwordHash,
            'id' => $userId,
        ]);
    }

    /**
     * Flips a user's role - used by vendor-request approval.
     * Separate from updateProfile()/updatePassword() for the same
     * reason those are separate from each other: a single-purpose
     * partial update can't accidentally clobber unrelated columns.
     */
    public function updateRole(int $userId, int $roleId): void
    {
        $sql = "
            UPDATE users
            SET role_id = :role_id
            WHERE id = :id
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'role_id' => $roleId,
            'id' => $userId,
        ]);
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

    public function countUsers(?string $search, ?int $roleId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM users u
            WHERE 1 = 1
        ";

        $parameters = [];

        $this->applyUserFilters($sql, $parameters, $search, $roleId);

        $statement = $this->prepare($sql);

        $statement->execute($parameters);

        return (int) $statement->fetchColumn();
    }

    public function findUsers(
        int $limit,
        int $offset,
        ?string $search,
        ?int $roleId,
        string $sortBy = 'id',
        string $sortDirection = 'DESC'
    ): array {

        // sort_by is already restricted to a fixed column
        // whitelist in UserValidator::ALLOWED_SORT_COLUMNS before
        // it ever reaches here, so interpolating it directly is
        // safe - same pattern ProductRepository::findProducts()
        // uses. sort_direction gets a second, defensive check
        // anyway, since it's cheap and this method could in
        // principle be called from elsewhere later.
        $sortDirection = strtoupper($sortDirection);

        if (!in_array($sortDirection, ['ASC', 'DESC'], true)) {
            $sortDirection = 'DESC';
        }

        $allowedSortColumns = ['id', 'username', 'email', 'role_id'];

        if (!in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'id';
        }

        $sql = "
            SELECT
                u.id,
                u.username,
                u.email,
                u.role_id,
                r.permission AS role_permission,
                u.email_verified_at,
                u.created_at
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            WHERE 1 = 1
        ";

        $parameters = [];

        $this->applyUserFilters($sql, $parameters, $search, $roleId);

        $sql .= " ORDER BY u.{$sortBy} {$sortDirection}";

        $sql .= " LIMIT :limit OFFSET :offset";

        $statement = $this->prepare($sql);

        foreach ($parameters as $key => $value) {
            $statement->bindValue(":{$key}", $value);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Shared WHERE-clause builder for countUsers()/findUsers() so
     * the two stay in sync - a filter added to one without the
     * other would make the pagination total silently disagree
     * with the rows actually returned.
     */
    private function applyUserFilters(
        string &$sql,
        array &$parameters,
        ?string $search,
        ?int $roleId
    ): void {

        if ($search !== null && $search !== '') {
            $sql .= " AND (u.username LIKE :search OR u.email LIKE :search)";
            $parameters['search'] = "%{$search}%";
        }

        if ($roleId !== null) {
            $sql .= " AND u.role_id = :role_id";
            $parameters['role_id'] = $roleId;
        }

    }
}