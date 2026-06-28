<?php
declare(strict_types=1);
class Response{
        /**
     * Structural application response client router redirect controller wrapper
     */
    private static function redirect($url) {
        header("Location: " . $url);
        exit();
    }
    public static function redirectAdmin($url){
        self::redirect(
        Config::app('admin_url') . $url
        );
    }
    public static function redirectBase($url){
        self::redirect(
        Config::app('base_url') . $url
        );
    }
    /**
     * Centralized Error Handler: Shuts down execution and renders the 403 Forbidden UI.
     */
        public static function forbidden() {
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

}