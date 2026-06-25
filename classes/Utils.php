<?php
require_once __DIR__ . '/config.php';
class Utils {
    
    /**
     * Instantiates a secure, long-lived session cookie mapping framework (Defeats Amnesia)
     * Enforces HttpOnly (XSS protection) and SameSite=Lax (CSRF mitigation).
     */
    public static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Keep users logged in for 14 days straight
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

    /**
     * Context-aware HTML Escaping for frontend rendering output contexts (Defeats XSS)
     */
    public static function escape($data) {
        if ($data === null) return '';
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Whitespace truncation input formatting (Preserves password/email character matrices)
     */
    public static function trimInput($data) {
        return trim($data);
    }

    /**
     * Structural application response client router redirect controller wrapper
     */
    public static function redirect($url) {
        header("Location: " . $url);
        exit();
    }

    /**
     * Cryptographic generation factory mapping secure layout form tokens (Anti-CSRF)
     */
    public static function getCSRFToken() {
        self::startSecureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Interception routing layer validating incoming state alteration signatures (Anti-CSRF)
     */
    public static function checkCSRF() {
        self::startSecureSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                self::show403();
            }
        }
    }
    /**
     * Centralized Error Handler: Shuts down execution and renders the 403 Forbidden UI.
     */
        public static function show403() {
        // 1. Tell the browser/attacker explicitly this is a 403 Forbidden request
        http_response_code(403);
        
        // 2. Resolve the path: __DIR__ is the classes/ folder. 
        // We step out of classes/ and step into the admin/ folder to find the template.
        $customErrorPage = __DIR__ . '/../admin/error-403.html';

        if (file_exists($customErrorPage)) {
            require_once $customErrorPage;
        } else {
            // Safe fallback layout if the file is ever missing or accidentally renamed
            echo "<h1>403 Forbidden</h1>";
            echo "<p>Your client does not have permission to get this page from the server.</p>";
        }
        
        // 3. Hard-kill execution to stop any remaining script logic or leaks
        exit();
    }
    /**
     * Generates a cache-busted URL for static assets (CSS, JS)
     * * @param string $relativePath Path to the asset relative to the admin folder (e.g., 'css/styles.css')
     * @return string The asset path appended with its modification timestamp
     */
    public static function asset(string $relativePath): string {
        // __DIR__ is the classes/ folder. We step out to find the physical file inside admin/
        $physicalPath = __DIR__ . '/../admin/' . $relativePath;

        if (file_exists($physicalPath)) {
            $timestamp = filemtime($physicalPath);
            return $relativePath . '?v=' . $timestamp;
        }

        // Fallback: If the file is missing, return the original string so the layout doesn't completely die
        return $relativePath;
    }
}