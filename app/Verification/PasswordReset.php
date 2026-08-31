<?php

declare(strict_types=1);

final class PasswordReset
{
    private PasswordResetRepository $resetRepository;
    private UserRepository $userRepository;

    public function __construct(PDO $pdo)
    {
        $this->resetRepository = new PasswordResetRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
    }

    /**
     * Generates a fresh reset token for the account matching
     * $email (if any), stores its hash, and emails the reset
     * link. Silent no-op when the email doesn't match an account -
     * the caller (forgot-password.php) always reports the same
     * generic success message either way, so this never confirms
     * or denies whether an email is registered.
     */
    public function issue(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            return;
        }

        $token = $this->generateToken();

        $selector = $this->generateSelector();

        $tokenHash = $this->hashToken($token);

        $expiresAt = new DateTimeImmutable(
            Config::app('password_reset.duration')
        );

        $this->resetRepository->createOrReplace(
            (int) $user['id'],
            $selector,
            $tokenHash,
            $expiresAt
        );

        $this->sendResetEmail($email, $selector, $token);
    }

    /**
     * Consumes a "selector:token" pair and returns the matching
     * user id on success, or null for any invalid, expired, or
     * unrecognised case - deliberately without distinguishing
     * which, same reasoning as EmailVerification::verify().
     *
     * Does NOT change the password itself; the caller
     * (UserService::resetPassword) still needs to validate the
     * new password before writing it, so this just confirms the
     * token is legitimate and hands back who it belongs to.
     */
    public function consume(string $selectorAndToken): ?int
    {
        $parts = explode(':', $selectorAndToken, 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$selector, $token] = $parts;

        if ($selector === '' || $token === '') {
            return null;
        }

        $reset = $this->resetRepository->findBySelector($selector);

        if ($reset === null) {
            return null;
        }

        if (
            new DateTimeImmutable() >
            new DateTimeImmutable($reset['expires_at'])
        ) {
            $this->resetRepository->deleteByUser(
                (int) $reset['user_id']
            );

            return null;
        }

        if (
            !hash_equals(
                $reset['token_hash'],
                $this->hashToken($token)
            )
        ) {
            return null;
        }

        $this->resetRepository->deleteByUser(
            (int) $reset['user_id']
        );

        return (int) $reset['user_id'];
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function generateSelector(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    private function sendResetEmail(
        string $email,
        string $selector,
        string $token
    ): void {

        $resetLink = Mailer::absoluteUrl(
            "reset-password.php?token={$selector}:{$token}"
        );

        $subject = 'Reset your Nexus password';

        $body = "Hi,\r\n\r\n"
            . "We received a request to reset your password. Click the link below to choose a new one:\r\n"
            . "{$resetLink}\r\n\r\n"
            . "If you didn't request this, you can safely ignore this email.\r\n"
            . "This link will expire in 1 hour.\r\n";

        Mailer::send($email, $subject, $body);
    }
}
