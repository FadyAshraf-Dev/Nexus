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

    error_log((string) $e);

    Response::json([

        'success' => false,

        'message' => 'An unexpected error occurred.',

    ], 500);

}