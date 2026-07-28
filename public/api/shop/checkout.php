<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1]);

try {

    $validator =
        OrderValidator::validateCheckout($_POST);

    if ($validator->fails()) {

        Response::json([

            "success" => false,

            "errors" => $validator->errors()

        ], 422);

    }

    $validated = $validator->validated();

    $service =
        new OrderService(Database::connection());

    $orderId = $service->placeOrder(

        Session::id(),

        $validated["address"],

        $validated["phone"]

    );

    Response::json([

        "success" => true,

        "message" => "Order placed successfully.",

        "order_id" => $orderId

    ]);

} catch (RuntimeException $exception) {

    Response::json([

        "success" => false,

        "message" => $exception->getMessage()

    ], 400);

} catch (Throwable $exception) {

    error_log($exception);

    Response::json([

        "success" => false,

        "message" =>
            "An unexpected error occurred."

    ], 500);

}