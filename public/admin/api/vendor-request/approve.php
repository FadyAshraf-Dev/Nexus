<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([Role::ADMIN]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

CSRF::verify();

try {

    $requestId = (int) ($_POST['id'] ?? 0);

    if ($requestId <= 0) {

        Response::json([
            'success' => false,
            'message' => 'Missing or invalid request id.',
        ], 422);

    }

    $service = new VendorRequestService(
        Database::connection()
    );

    $service->approve($requestId, Session::id());

    Response::json([
        'success' => true,
        'message' => 'Vendor request approved.',
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
