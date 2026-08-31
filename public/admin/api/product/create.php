<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

// Any VENDOR or ADMIN may create a product - always as themselves.
// There is no "create on behalf of another vendor_id" path; that
// value is never read from the request (see below).
Gatekeeper::authorize([
    Role::VENDOR,
    Role::ADMIN,
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

CSRF::verify();

try {

    $validator = ProductValidator::validate($_POST);

    $imageCountError = ProductValidator::validateImageCount(
        $_FILES['images'] ?? ['name' => []]
    );

    if ($imageCountError !== null) {
        $validator->addError('images', $imageCountError);
    }

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'message' => 'Please check your input and try again.',
            'errors' => $validator->errors(),
        ], 422);

    }

    $data = $validator->validated();

    // vendor_id is always the logged-in user - never trust a
    // client-supplied value here, or any vendor/admin could
    // create products under someone else's account.
    $data['vendor_id'] = Session::id();

    $productService = new ProductService(
        Database::connection()
    );

    $productId = $productService->createProduct(
        $data,
        $_FILES['images']
    );

    Response::json([
        'success' => true,
        'message' => 'Product created.',
        'data' => [
            'id' => $productId,
        ],
    ], 201);

} catch (InvalidArgumentException $e) {

    Response::json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 422);

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
