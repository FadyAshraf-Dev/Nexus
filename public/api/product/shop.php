<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

try {

    $validator = ProductValidator::validateListing($_GET);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors'  => $validator->errors(),
        ], 422);

    }

    $filters = $validator->validated();

    $productService = new ProductService(Database::connection());

    $result = $productService->getProducts($filters);

    $products = array_map(

        static function (array $product): array {

            $sellingPrice = (float) $product['selling_price'];

            $displayPrice = $sellingPrice;

            if ($product['discount_type'] === 'relative') {

                $displayPrice -=
                    $sellingPrice *
                    ((float) $product['discount_value'] / 100);

            } elseif ($product['discount_type'] === 'fixed') {

                $displayPrice -=
                    (float) $product['discount_value'];

            }
            
            $displayPrice = max(0, $displayPrice);

            return [

                'id' => (int) $product['id'],

                'slug' => $product['slug'],

                'product_name' => $product['product_name'],

                'image_path' => $product['image_path'],

                'display_price' => round($displayPrice, 2),

                'old_price' =>
                    $product['discount_type'] !== ""
                        ? round($sellingPrice, 2)
                        : null,

                'is_on_sale' =>
                    $product['discount_type'] !== "",
            ];

        },

        $result['products']

    );

    Response::json([

        'success' => true,

        'data' => [

            'products' => $products,

            'pagination' => $result['pagination'],

            'filters' => $result['filters'],

        ],

    ]);

} catch (Throwable $e) {

    Response::json([

        'success' => false,

        'message' => $e->getMessage(),

    ], 500);

}