<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([Role::ADMIN]);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

try {

    $status = $_GET['status'] ?? null;

    if ($status !== null && !in_array($status, ['pending', 'approved', 'rejected'], true)) {
        $status = null;
    }

    $repository = new VendorRequestRepository(
        Database::connection()
    );

    Response::json([
        'success' => true,
        'data' => $repository->findAll($status),
    ]);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}
