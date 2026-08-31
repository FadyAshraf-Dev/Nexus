<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

CSRF::verify();

try {

    $validator = CartValidator::validateUpdate($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors'  => $validator->errors(),
        ], 422);

    }

    $data = $validator->validated();

    $cartService = new CartService(
        Database::connection()
    );

    $cartService->updateQuantity(
        $data['product_id'],
        $data['quantity']
    );

    Response::json([

        'success' => true,

        'message' => 'Cart updated successfully.',

        'data' => [

            'cart_count' => $cartService->getItemCount(),

        ],

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