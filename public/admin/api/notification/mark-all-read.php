<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1, 2, 3]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);
}

try {

    $service = new NotificationService(Database::connection());

    $service->markAllRead(Session::id());

    Response::json([
        'success' => true,
    ]);

} catch (Throwable $exception) {

    error_log((string) $exception);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}