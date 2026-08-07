<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.',
    ], 405);
}

try {

    $validator = ReviewValidator::validateProductId($_GET);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $productId = (int) $validator->validated()['product_id'];

    $service = new ReviewService(Database::connection());

    $reviews = $service->getForProduct($productId);

    // The "can this visitor review this product" state is only
    // meaningful for a logged-in user - guests just see the reviews
    // list with no submission form.
    $viewerState = [
        'can_review' => false,
        'own_review' => null,
    ];

    if (!Session::guest()) {

        $userId = Session::id();

        $ownReview = $service->getUserReviewForProduct($userId, $productId);

        if ($ownReview !== null) {
            $viewerState['own_review'] = $ownReview;
        } else {
            $eligibility = $service->checkEligibility($userId, $productId);
            $viewerState['can_review'] = $eligibility['eligible'];
        }

    }

    Response::json([
        'success' => true,
        'data' => [
            'reviews' => $reviews,
            'viewer' => $viewerState,
        ],
    ]);

} catch (Throwable $exception) {

    error_log((string) $exception);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}