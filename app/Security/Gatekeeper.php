<?php
declare(strict_types=1);
// classes/Gatekeeper.php
class Gatekeeper {
    /**
     * Enforces role-based access control using the session user array.
     *
     * @param array $roles Array of permitted role IDs.
     * @param string $loginRedirect Path to the login screen for unauthenticated users.
     */
    public static function authorize(array $roles, ?string $loginRedirect=null) {
        // 1. Ensure the session is running
        Session::start();

        // 2. Authentication Check: Is the user array set in the session?
        if (Session::guest()) {
            // Determine where they need to go
            $fallback = $loginRedirect ?? 'login.php';
            
            // Clean, consistent, and handles the exit() internally!
            Response::redirectAdmin($fallback);
        }


        // 4. Authorization Check: Does their role match the allowed list?
        if (!in_array(Session::roleId(), $roles, true)) {
            
            // Upgraded: Pass control to your centralized absolute-path utility!
            Response::forbidden();
        }
    }

}