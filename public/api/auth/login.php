<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

// Gatekeeper::guestOnly();

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

//     Response::json([
//         'success' => false,
//         'message' => 'Method Not Allowed.',
//     ], 405);

// }

CSRF::verify();

try {

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {

        Response::json([
            'success' => false,
            'message' => 'Email and password are required.',
        ], 422);

    }

    $rememberMe = filter_var(
        $_POST['remember_me'] ?? false,
        FILTER_VALIDATE_BOOLEAN
    );

    $authService = new AuthenticationService(
        Database::connection()
    );

    $user = $authService->login($email, $password, $rememberMe);

    Response::json([
        'success' => true,
        'message' => 'Logged in.',
        'user' => [
            'id' => (int) $user['id'],
            'email' => $user['email'],
            'username' => $user['username'],
            'role_id' => (int) $user['role_id'],
        ],
    ]);

} catch (RuntimeException $e) {

    Response::json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 401);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}