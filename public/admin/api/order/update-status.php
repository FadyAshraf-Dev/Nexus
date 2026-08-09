<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([
    Role::ADMIN,
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

try {

    // See public/api/review/update.php for why $_POST is read directly
    // rather than via filter_input(INPUT_POST, ...).
    $orderId = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$orderId) {
        Response::json([
            'success' => false,
            'message' => 'Invalid order id.',
        ], 422);
    }

    $validator = OrderValidator::validateStatusUpdate($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $validated = $validator->validated();

    $orderService = new OrderService(
        Database::connection()
    );

    $updated = $orderService->updateStatus(
        $orderId,
        $validated['status']
    );

    if (!$updated) {
        Response::json([
            'success' => false,
            'message' => 'Order not found.',
        ], 404);
    }

    Response::json([
        'success' => true,
        'message' => 'Order status updated.',
    ]);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}