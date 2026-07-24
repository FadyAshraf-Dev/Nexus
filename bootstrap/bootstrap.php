<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/bootstrap/autoload.php';
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
Config::load();
Session::start();
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (str_contains($contentType, 'application/json')) {

    $json = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (is_array($json)) {
        $_POST = $json;
    }

}
if (Session::guest()) {

    try {

        $authentication = new AuthenticationService(
            Database::connection()
        );

        $authentication->restore();

    } catch (Throwable $e) {

        /*
         * Never stop the request because
         * Remember Me failed.
         *
         * Worst case:
         * user stays logged out.
         */

        error_log($e);

    }

}