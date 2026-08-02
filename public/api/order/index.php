<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1,2,3]);

try {

    $service = new OrderService(Database::connection());

    $orders = $service->findUserOrders(Session::id());

    Response::json([
        "success" => true,
        "data" => $orders
    ]);

} catch (Throwable $exception) {

    error_log((string) $exception);

    Response::json([
        "success" => false,
        "message" => "An unexpected error occurred."
    ], 500);

}