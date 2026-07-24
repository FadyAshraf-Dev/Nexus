<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

try {

    $cartService = new CartService(
        Database::connection()
    );

    $items = $cartService->getCart();

    Response::json([

        'success' => true,

        'data' => [

            'items' => $items,

            'item_count' => $cartService->getItemCount(),

        ],

    ]);

} catch (Throwable $e) {

    Response::json([

        'success' => false,

        'message' => $e->getMessage(),

    ], 500);

}