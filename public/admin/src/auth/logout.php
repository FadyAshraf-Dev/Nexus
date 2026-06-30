<?php
declare(strict_types=1);
// 1. Load the database/config bootstrap which starts the session globally
require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';
// 2. Unset all session superglobal variables in-memory
Session::destroy();
// 5. Use your global APP_URL base path to safely route them back to the login gateway
// This passes the 'logged_out' flag to trigger your beautiful green success alert banner!
Flash::success("Logged Out Successfully.");

Response::redirectAdmin("login.php");