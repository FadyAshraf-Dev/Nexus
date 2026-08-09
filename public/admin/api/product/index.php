<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([
    Role::ADMIN,
]);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

try {

    $validator = ProductValidator::validateListing($_GET);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $productService = new ProductService(
        Database::connection()
    );

    // vendorId = null - unlike the vendor-side endpoint (which scopes to
    // Session::id()), admin management lists products across all vendors.
    $result = $productService->getProducts(
        $validator->validated(),
        null
    );

    Response::json([
        'success' => true,
        'data' => $result,
    ]);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}