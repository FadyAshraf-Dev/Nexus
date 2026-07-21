<?php
declare(strict_types=1);
class CSRF
{
    /**
     * Cryptographic generation factory mapping secure layout form tokens (Anti-CSRF)
     */
    public static function token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Interception routing layer validating incoming state alteration signatures (Anti-CSRF)
     */
    public static function verify(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $submittedToken = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        if (
            !$submittedToken ||
            !hash_equals(
                self::token(),
                $submittedToken
            )
        ) {
            Response::forbidden();
        }
    }

}