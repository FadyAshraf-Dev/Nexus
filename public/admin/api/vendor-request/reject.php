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

    $validator = VendorRequestValidator::validateReject($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'message' => 'Please provide a reason.',
            'errors' => $validator->errors(),
        ], 422);

    }

    $data = $validator->validated();

    $service = new VendorRequestService(
        Database::connection()
    );

    $service->reject($requestId, Session::id(), $data['reason']);

    Response::json([
        'success' => true,
        'message' => 'Vendor request rejected.',
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
