<?php
declare(strict_types=1);
final class EmailVerification
{
    private EmailVerificationRepository $verificationRepository;
    private UserRepository $userRepository;

    public function __construct(PDO $pdo)
    {
        $this->verificationRepository = new EmailVerificationRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
    }

    /**
     * Generates a fresh verification code for a user, stores its
     * hash, and emails the activation link. Called on
     * registration, and reusable later for a "resend" action -
     * createOrReplace() means a resend simply overwrites the
     * previous unclicked code.
     */
    public function issue(int $userId, string $email): void
    {
        $code = $this->generateCode();

        $selector = $this->generateSelector();

        $codeHash = $this->hashCode($code);

        $expiresAt = new DateTimeImmutable(
            Config::app('email_verification.duration')
        );

        $this->verificationRepository->createOrReplace(
            $userId,
            $selector,
            $codeHash,
            $expiresAt
        );

        $this->sendVerificationEmail($email, $selector, $code);
    }
    
    private function generateCode(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function generateSelector(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function hashCode(string $code): string
    {
        return hash('sha256', $code);
    }

    private function sendVerificationEmail(
        string $email,
        string $selector,
        string $code
    ): void {

        // Points at the HTML page (activate.php), not the raw
        // JSON endpoint (api/auth/activate.php) - a link clicked
        // from an email client is a browser navigation expecting
        // a page, not a fetch call. The page itself calls the API
        // endpoint client-side. See public/activate.php.
        $activationLink = Mailer::absoluteUrl(
            "activate.php?token={$selector}:{$code}"
        );

        $subject = 'Please activate your Nexus account';

        $body = "Hi,\r\n\r\n"
            . "Please click the following link to activate your account:\r\n"
            . "{$activationLink}\r\n\r\n"
            . "This link will expire in 24 hours.\r\n";

        Mailer::send($email, $subject, $body);
    }

    /**
     * Consumes a "selector:code" pair (as received from the
     * activation link's query string) and marks the matching
     * user's email as verified.
     *
     * Returns true on success. Returns false for any invalid,
     * expired, or already-used case - deliberately without
     * distinguishing which, so a stale/tampered link can't be
     * used to probe which selectors are real.
     */
    public function verify(string $selectorAndCode): bool
    {
        $parts = explode(':', $selectorAndCode, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$selector, $code] = $parts;

        if ($selector === '' || $code === '') {
            return false;
        }

        $verification = $this->verificationRepository->findBySelector($selector);

        if ($verification === null) {
            return false;
        }

        if (
            new DateTimeImmutable() >
            new DateTimeImmutable($verification['expires_at'])
        ) {
            $this->verificationRepository->deleteByUser(
                (int) $verification['user_id']
            );

            return false;
        }

        if (
            !hash_equals(
                $verification['code_hash'],
                $this->hashCode($code)
            )
        ) {
            return false;
        }

        $this->userRepository->markEmailVerified(
            (int) $verification['user_id']
        );

        $this->verificationRepository->deleteByUser(
            (int) $verification['user_id']
        );

        return true;
    }

}