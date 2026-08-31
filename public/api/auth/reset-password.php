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

    $validator = UserValidator::validateResetPassword($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'message' => 'Please check your input and try again.',
            'errors' => $validator->errors(),
        ], 422);

    }

    $data = $validator->validated();

    $userService = new UserService(
        Database::connection()
    );

    $userService->resetPassword($data['token'], $data['password']);

    Response::json([
        'success' => true,
        'message' => 'Your password has been reset. You can now sign in.',
    ]);

} catch (RuntimeException $e) {

    Response::json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 400);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}
