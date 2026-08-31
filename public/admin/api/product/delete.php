<?php
declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

// VENDOR can delete their own products; ADMIN can delete ANY
// product as a disciplinary override (see the ownership check
// below) - so both roles need to reach this endpoint, not just
// ADMIN.
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

    // See public/api/review/update.php for why $_POST is read directly
    // rather than via filter_input(INPUT_POST, ...).
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

    // Unlike update.php, ADMIN gets an override here: delete is
    // owner OR admin (disciplinary removal of any listing).
    // VENDOR has no override - only their own products.
    $isOwner = (int) $existingProduct['vendor_id'] === Session::id();

    $isAdmin = Session::roleId() === Role::ADMIN;

    if (!$isOwner && !$isAdmin) {

        Response::json([
            'success' => false,
            'message' => 'You do not have permission to delete this product.',
        ], 403);

    }

    $productService = new ProductService(
        Database::connection()
    );

    $productService->deleteProduct($productId);

    Response::json([
        'success' => true,
        'message' => 'Product deleted.',
    ]);

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