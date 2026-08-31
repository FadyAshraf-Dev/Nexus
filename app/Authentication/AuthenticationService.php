<?php

declare(strict_types=1);

final class AuthenticationService
{
    private PDO $pdo;
    private UserRepository $users;
    private Token $token;
    private CartService $cart;
    private EmailVerification $emailVerification;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->users = new UserRepository($pdo);
        $this->token = new Token($this->pdo);
        $this->cart = new CartService($pdo);
        $this->emailVerification = new EmailVerification($pdo);
    }

    /**
     * Registers a new customer account and sends the activation
     * email. The account exists immediately but email_verified_at
     * stays NULL until they click the link - login() blocks
     * unverified accounts.
     *
     * @throws RuntimeException
     */
    public function register(
        string $email,
        string $username,
        string $password
    ): int {

        if ($this->users->findByEmail($email) !== null) {
            throw new RuntimeException(
                'An account with that email already exists.'
            );
        }

        if ($this->users->findByUsername($username) !== null) {
            throw new RuntimeException(
                'That username is already taken.'
            );
        }

        $userId = $this->users->create([
            'email' => $email,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id' => Role::CUSTOMER,
        ]);

        $this->emailVerification->issue($userId, $email);

        return $userId;
    }

    /**
     * Authenticates a user.
     *
     * Workflow:
     *  1. Find user by email.
     *  2. Verify password.
     *  3. Confirm the account's email has been verified.
     *  4. Start authenticated session.
     *  5. Optionally issue a Remember Me token.
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

        if (!password_verify($password, $user['password'])) {
            throw new RuntimeException(
                'Invalid email or password.'
            );
        }

        if ($user['email_verified_at'] === null) {
            throw new RuntimeException(
                'Please verify your email address before logging in.'
            );
        }

        Session::login([
            'id' => $user['id'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
        ]);
        $this->cart->mergeCookieIntoDatabase();

        if ($rememberMe) {

            $this->token->create(
                (int) $user['id']
            );
        }

        return $user;
    }

    /**
     * Consumes an activation link's "selector:code" token and
     * marks the matching account's email as verified.
     */
    public function verifyEmail(string $selectorAndCode): bool
    {
        return $this->emailVerification->verify($selectorAndCode);
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