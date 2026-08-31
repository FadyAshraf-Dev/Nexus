<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

// No Gatekeeper::authorize() here - this endpoint is guest-only
// by definition (registering a *new* account). authorize() always
// *requires* an existing session and redirects to an HTML login
// page on failure - guestOnly() is the mirror image of that,
// built for exactly this case.
Gatekeeper::guestOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

CSRF::verify();

try {

    $validator = UserValidator::validateRegistration($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $validated = $validator->validated();

    $authService = new AuthenticationService(
        Database::connection()
    );

    $userId = $authService->register(
        $validated['email'],
        $validated['username'],
        $validated['password']
    );

    Response::json([
        'success' => true,
        'message' => 'Account created. Please check your email to activate your account.',
        'user_id' => $userId,
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