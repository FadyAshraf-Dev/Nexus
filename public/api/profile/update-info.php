<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([
    Role::CUSTOMER,
    Role::VENDOR,
    Role::ADMIN,
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

CSRF::verify();

try {

    $validator = UserValidator::validateProfileUpdate($_POST);

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

    $userService->updateProfile(
        Session::id(),
        $data['email'],
        $data['username']
    );

    Response::json([
        'success' => true,
        'message' => 'Profile updated successfully.',
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
