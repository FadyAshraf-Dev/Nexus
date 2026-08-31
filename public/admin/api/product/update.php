<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

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

    $productId = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$productId) {

        Response::json([
            'success' => false,
            'message' => 'Invalid product id.',
        ], 422);

    }

    $productRepository = new ProductRepository(
        Database::connection()
    );

    $existingProduct = $productRepository->findById($productId);

    if ($existingProduct === null) {

        Response::json([
            'success' => false,
            'message' => 'Product not found.',
        ], 404);

    }

    // Owner-only, no exceptions - unlike delete.php, ADMIN gets
    // no override here. Editing someone else's listing (changing
    // its price, description, stock...) is a different act from
    // removing it, so admin's disciplinary delete-override does
    // not extend to edit. See public/admin/api/product/delete.php
    // for the contrasting rule.
    if ((int) $existingProduct['vendor_id'] !== Session::id()) {

        Response::json([
            'success' => false,
            'message' => 'You do not have permission to edit this product.',
        ], 403);

    }

    $validator = ProductValidator::validate($_POST);

    $imageCountError = null;

    $hasNewImages = !empty(
        array_filter(
            $_FILES['images']['name'] ?? [],
            fn($name) => $name !== ''
        )
    );

    // Images are optional on update - a vendor resaving the rest
    // of the form shouldn't be forced to re-upload photos that
    // are already there. Only validate the count when new files
    // were actually attached.
    if ($hasNewImages) {

        $imageCountError = ProductValidator::validateImageCount(
            $_FILES['images']
        );

    }

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

    $productService = new ProductService(
        Database::connection()
    );

    $productService->updateProduct(
        $productId,
        $validator->validated(),
        $_FILES['images'] ?? ['name' => []]
    );

    Response::json([
        'success' => true,
        'message' => 'Product updated.',
    ]);

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
