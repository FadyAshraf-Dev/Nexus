<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::guestOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

try {

    $token = (string) ($_GET['token'] ?? '');

    if ($token === '') {

        Response::json([
            'success' => false,
            'message' => 'Missing activation token.',
        ], 422);

    }

    $authService = new AuthenticationService(
        Database::connection()
    );

    $verified = $authService->verifyEmail($token);

    if (!$verified) {

        // Deliberately generic - EmailVerification::verify() does
        // not distinguish invalid/expired/already-used, so a
        // stale or tampered link can't be used to probe which
        // selectors are real. See that class for the full reasoning.
        Response::json([
            'success' => false,
            'message' => 'This activation link is invalid or has expired.',
        ], 400);

    }

    Response::json([
        'success' => true,
        'message' => 'Your email has been verified. You can now log in.',
    ]);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}