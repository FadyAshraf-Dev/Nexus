<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::guestOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

CSRF::verify();

try {

    $validator = UserValidator::validateForgotPassword($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'message' => 'Please enter a valid email address.',
            'errors' => $validator->errors(),
        ], 422);

    }

    $data = $validator->validated();

    $userService = new UserService(
        Database::connection()
    );

    $userService->requestPasswordReset($data['email']);

    // Always the same message, whether or not that email is
    // registered - see UserService::requestPasswordReset().
    Response::json([
        'success' => true,
        'message' => 'If an account exists for that email, a reset link is on its way.',
    ]);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}
