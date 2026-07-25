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

    $subtotal = 0.0;

    $formattedItems = array_map(
        static function (array $item) use (&$subtotal): array {

            /**
             * Business Logic
             */

            $sellingPrice = (float) $item['selling_price'];

            $discountValue = (float) ($item['discount_value'] ?? 0);

            $quantity = (int) $item['quantity'];

            switch ($item['discount_type']) {

                case 'fixed':
                    $unitPrice = max(
                        0,
                        $sellingPrice - $discountValue
                    );
                    break;

                case 'percentage':
                    $unitPrice = $sellingPrice * (
                        1 - ($discountValue / 100)
                    );
                    break;

                default:
                    $unitPrice = $sellingPrice;
            }

            $lineTotal = $unitPrice * $quantity;

            $subtotal += $lineTotal;

            /**
             * Presentation Model
             */

            return [

                'id' => (int) $item['id'],

                'product_id' => (int) $item['product_id'],

                'product_name' => $item['product_name'],

                'image_path' => $item['image_path'],

                'quantity' => $quantity,

                'unit_price' => number_format(
                    $unitPrice,
                    2,
                    '.',
                    ''
                ),

                'line_total' => number_format(
                    $lineTotal,
                    2,
                    '.',
                    ''
                ),

            ];
        },
        $items
    );

    Response::json([

        'success' => true,

        'data' => [

            'items' => $formattedItems,

            'item_count' => $cartService->getItemCount(),

            'subtotal' => number_format(
                $subtotal,
                2,
                '.',
                ''
            ),

            'total' => number_format(
                $subtotal,
                2,
                '.',
                ''
            ),

        ],

    ]);

} catch (Throwable $e) {

    Response::json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 500);
}