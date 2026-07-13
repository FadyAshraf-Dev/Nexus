<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([
    Role::VENDOR,
]);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);
}

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

$result = $productService->getVendorProducts(
    Session::id(),
    $validator->validated()
);

Response::json([
    'success' => true,
    'data' => $result,
]);