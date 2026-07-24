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

    $cartService = new CartService(
        Database::connection()
    );

    $cartService->clear();

    Response::json([

        'success' => true,

        'message' => 'Cart cleared successfully.',

        'data' => [

            'cart_count' => 0,

        ],

    ]);

} catch (Throwable $e) {

    Response::json([

        'success' => false,

        'message' => $e->getMessage(),

    ], 500);

}