<?php
declare(strict_types=1);
class Session
{
    /**
     * Instantiates a secure, long-lived session cookie mapping framework (Defeats Amnesia)
     * Enforces HttpOnly (XSS protection) and SameSite=Lax (CSRF mitigation).
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Keep users logged in for a month
            $lifetime = 30 * 24 * 60 * 60;

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '', // Contextually defaults to current host domain
                'secure' => isset($_SERVER['HTTPS']), // Enforces HTTPS if active
                'httponly' => true,  // Anti-XSS: Prevents JS from stealing the session cookie ID
                'samesite' => 'Lax'  // Anti-CSRF: Restricts cross-site cookie payload deliveries
            ]);

            session_start();
        }
    }
    public static function destroy(): void
    {
        session_unset();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        // Start a brand-new clean session
        self::start();

        // Give it a fresh session ID
        session_regenerate_id(true);
    }
}