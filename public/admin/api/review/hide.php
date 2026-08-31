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

CSRF::verify();

try {

    // See public/api/review/update.php for why $_POST is read directly
    // rather than via filter_input(INPUT_POST, ...) - the latter never
    // sees bootstrap.php's JSON-body-decoded $_POST reassignment.
    $reviewId = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$reviewId) {
        Response::json([
            'success' => false,
            'message' => 'Invalid review id.',
        ], 422);
    }

    $reviewService = new ReviewService(
        Database::connection()
    );

    $reviewService->remove($reviewId);

    Response::json([
        'success' => true,
        'message' => 'Review hidden.',
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