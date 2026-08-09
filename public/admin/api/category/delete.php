<?php

declare(strict_types=1);

require_once dirname(__DIR__, 4) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([
    Role::ADMIN,
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);

}

try {

    $categoryId = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$categoryId) {
        Response::json([
            'success' => false,
            'message' => 'Invalid category id.',
        ], 422);
    }

    $categoryService = new CategoryService(
        Database::connection()
    );

    $categoryService->deleteCategory($categoryId);

    Response::json([
        'success' => true,
        'message' => 'Category deleted.',
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