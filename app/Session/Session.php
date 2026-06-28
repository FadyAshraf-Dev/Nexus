<?php
declare(strict_types=1);
Class Session{
    /**
     * Instantiates a secure, long-lived session cookie mapping framework (Defeats Amnesia)
     * Enforces HttpOnly (XSS protection) and SameSite=Lax (CSRF mitigation).
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Keep users logged in for a month
            $lifetime = 30 * 24 * 60 * 60; 
            
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => '/',
                'domain'   => '', // Contextually defaults to current host domain
                'secure'   => isset($_SERVER['HTTPS']), // Enforces HTTPS if active
                'httponly' => true,  // Anti-XSS: Prevents JS from stealing the session cookie ID
                'samesite' => 'Lax'  // Anti-CSRF: Restricts cross-site cookie payload deliveries
            ]);
            
            session_start();
        }
    }
    public static function destory(){
        session_unset();

        // 3. Clear the session cookie from the user's browser completely
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000, // Back-date expiration time to make the browser delete it instantly
                $params["path"], 
                $params["domain"], 
                $params["secure"], 
                $params["httponly"]
            );
        }

        // 4. Wipe out the session data payload registry on the web server
        session_destroy();

    }

}