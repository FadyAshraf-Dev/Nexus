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

CSRF::verify();

try {

    $validator = ReviewValidator::validateSubmit($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $validated = $validator->validated();

    $service = new ReviewService(Database::connection());

    $reviewId = $service->submit(
        Session::id(),
        (int) $validated['product_id'],
        (int) $validated['rate'],
        $validated['content'] ?? null
    );

    Response::json([
        'success' => true,
        'message' => 'Review submitted.',
        'review_id' => $reviewId,
    ]);

} catch (RuntimeException $exception) {

    Response::json([
        'success' => false,
        'message' => $exception->getMessage(),
    ], 400);

} catch (Throwable $exception) {

    error_log((string) $exception);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}