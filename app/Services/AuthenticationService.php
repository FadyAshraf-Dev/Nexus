<?php

declare(strict_types=1);

final class AuthenticationService
{
    private PDO $pdo;
    private UserRepository $users;
    private Token $token;

    public function __construct(PDO $pdo) {
    $this->pdo = $pdo;
    $this->users = new UserRepository($pdo);
    $this->token = new Token($this->pdo);
    }

    /**
     * Authenticates a user.
     *
     * Workflow:
     *  1. Find user by email.
     *  2. Verify password.
     *  3. Start authenticated session.
     *  4. Optionally issue a Remember Me token.
     *
     * @throws RuntimeException
     */
    public function login(
        string $email,
        string $password,
        bool $rememberMe = false
    ): array {

        $user = $this->users->findByEmail($email);

        if (!$user) {
            throw new RuntimeException(
                'Invalid email or password.'
            );
        }

        /*
         * Future:
         *
         * if (!password_verify($password, $user['password'])) {
         *      throw new RuntimeException(...);
         * }
         */

        if ($user['password'] !== $password) {
            throw new RuntimeException(
                'Invalid email or password.'
            );
        }

        Session::login([
            'id' => $user['id'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
        ]);

        if ($rememberMe) {
            
            $this->token->create(
                (int) $user['id']
            );
        }

        return $user;
    }

    /**
     * Logs out the currently authenticated user.
     *
     * Workflow:
     *  1. Delete Remember Me token.
     *  2. Destroy PHP session.
     */
    public function logout(?int $userId): void
    {
        if ($userId !== null) {
            $this->token->destroy($userId);
        }

        Session::logout();
    }
    /**
     * Attempts automatic authentication from
     * the Remember Me cookie.
     *
     * Returns true if authentication succeeded.
     */
    public function restore(): bool
    {
        return $this->token->restore();
    }
}