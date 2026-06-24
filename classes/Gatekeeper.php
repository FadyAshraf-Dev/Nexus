<?php
// classes/Gatekeeper.php
require_once __DIR__ . '/Utils.php';
class Gatekeeper {
    /**
     * Enforces role-based access control using the session user array.
     *
     * @param array $allowedRoleIds Array of permitted role IDs.
     * @param string $loginRedirect Path to the login screen for unauthenticated users.
     */
    public static function allow(array $allowedRoleIds, string $loginRedirect = null) {
        // 1. Ensure the session is running
        Utils::startSecureSession();

        // 2. Authentication Check: Is the user array set in the session?
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            // Determine where they need to go
            $fallback = $loginRedirect ?? (ADMIN_URL . 'login.php');
            
            // Clean, consistent, and handles the exit() internally!
            Utils::redirect($fallback);
        }

        // 3. Extract the role_id safely from the session user array
        $userRoleId = $_SESSION['user']['role_id'] ?? null;

        // 4. Authorization Check: Does their role match the allowed list?
        if (!in_array($userRoleId, $allowedRoleIds)) {
            
            // Upgraded: Pass control to your centralized absolute-path utility!
            Utils::show403();
        }
    }
}