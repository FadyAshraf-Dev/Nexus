<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1, 2, 3]);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);
}

try {

    $validator = NotificationValidator::validateIndex($_GET);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $validated = $validator->validated();

    $limit = isset($validated['limit'])
        ? (int) $validated['limit']
        : 10;

    $service = new NotificationService(Database::connection());

    $userId = Session::id();

    Response::json([
        'success' => true,
        'data' => [
            'notifications' => $service->getForUser($userId, $limit),
            'unread_count' => $service->getUnreadCount($userId),
        ],
    ]);

} catch (Throwable $exception) {

    error_log((string) $exception);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}