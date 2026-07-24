<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}
try {

    $validator = CartValidator::validateRemove($_POST);

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

    $cartService->removeItem(
        $data['product_id']
    );

    Response::json([

        'success' => true,

        'message' => 'Item removed from cart.',

        'data' => [

            'cart_count' => $cartService->getItemCount(),

        ],

    ]);

} catch (Throwable $e) {

    Response::json([

        'success' => false,

        'message' => $e->getMessage(),

    ], 500);

}