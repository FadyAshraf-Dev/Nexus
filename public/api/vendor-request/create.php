<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([Role::CUSTOMER]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

CSRF::verify();

try {

    $validator = VendorRequestValidator::validateCreate($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'message' => 'Please check your input and try again.',
            'errors' => $validator->errors(),
        ], 422);

    }

    $data = $validator->validated();

    $service = new VendorRequestService(
        Database::connection()
    );

    $service->apply(Session::id(), $data);

    Response::json([
        'success' => true,
        'message' => 'Your application has been submitted for review. We\'ll email you once it\'s decided.',
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
