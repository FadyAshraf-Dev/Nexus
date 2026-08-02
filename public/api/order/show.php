<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1,2,3]);

try {

    $orderId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$orderId) {

        Response::json([
            "success" => false,
            "message" => "Invalid order id."
        ], 422);

    }

    $service = new OrderService(Database::connection());

    $order = $service->findOrder($orderId);

    if (
        $order === null ||
        (int) $order["user_id"] !== Session::id()
    ) {
        // Same response whether the order doesn't exist or belongs to
        // someone else - don't leak which one it is.
        Response::json([
            "success" => false,
            "message" => "Order not found."
        ], 404);
    }

    Response::json([
        "success" => true,
        "data" => $order
    ]);

} catch (Throwable $exception) {

    error_log((string) $exception);

    Response::json([
        "success" => false,
        "message" => "An unexpected error occurred."
    ], 500);

}