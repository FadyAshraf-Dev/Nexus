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
    public static function login(array $user): void
    {
        self::start();

        self::regenerate();

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'email' => $user['email'],
            'role_id' => (int) $user['role_id'],
        ];
    }

    public static function logout(): void
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
        self::regenerate();

    }
    public static function check(): bool
    {
        return isset($_SESSION['user'])
            && is_array($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return self::user()["id"] ?? null;

    }

    public static function roleId(): ?int
    {
        return self::user()["role_id"] ?? null;

    }

    public static function guest(): bool
    {
        return !self::check();
    }
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}