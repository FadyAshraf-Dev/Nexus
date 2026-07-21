<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

try {

    $validator = ProductValidator::validateDetails($_GET);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $productService = new ProductService(Database::connection());

    $result = $productService->getProductDetails(
        $validator->validated()['slug']
    );

    $product = $result['product'];

    $sellingPrice = (float) $product['selling_price'];

$displayPrice = Price::calculatePrice(
    $sellingPrice,
    $product['discount_type'],
    $product['discount_value'],
);
    $relatedProducts = array_map(

        static function (array $related): array {

            $sellingPrice = (float) $related['selling_price'];

            $displayPrice = Price::calculatePrice(
                $sellingPrice,
                $related['discount_type'],
                $related['discount_value']
            );

            return [

                'id' => (int) $related['id'],

                'slug' => $related['slug'],

                'product_name' => $related['product_name'],

                'image_path' => $related['image_path'],

                'display_price' => round($displayPrice, 2),

                'old_price' => $displayPrice < $sellingPrice
                    ? round($sellingPrice, 2)
                    : null,

            ];

        },

        $result['related_products']

    );

    Response::json([

        'success' => true,

        'data' => [

            'id' => (int) $product['id'],

            'slug' => $product['slug'],

            'product_name' => $product['product_name'],

            'brand' => $product['brand'],

            'category_name' => $product['category_name'],

            'short_description' => $product['short_description'],

            'full_description' => $product['full_description'],

            'display_price' => round($displayPrice, 2),

            'old_price' => $displayPrice < $sellingPrice
                ? round($sellingPrice, 2)
                : null,

            'stock_quantity' => (int) $product['stock_quantity'],

            'images' => $product['images'],

            'related_products' => $relatedProducts,

        ],

    ]);

} catch (Throwable $e) {

    Response::json([

        'success' => false,

        'message' => $e->getMessage(),

    ], 500);

}