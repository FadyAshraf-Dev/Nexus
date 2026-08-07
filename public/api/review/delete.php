<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1, 2, 3]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);
}

try {

    // See update.php for why filter_input(INPUT_POST) doesn't work here.
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

    $service = new ReviewService(Database::connection());

    $review = $service->getById($reviewId);

    if ($review === null || (int) $review['user_id'] !== Session::id()) {
        // Same response whether it doesn't exist or belongs to someone
        // else - don't leak which one it is.
        Response::json([
            'success' => false,
            'message' => 'Review not found.',
        ], 404);
    }

    $service->remove($reviewId);

    Response::json([
        'success' => true,
        'message' => 'Review deleted.',
    ]);

} catch (Throwable $exception) {

    error_log((string) $exception);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}