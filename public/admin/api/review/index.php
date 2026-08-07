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

    $validator = ReviewValidator::validateListing($_GET);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $reviewService = new ReviewService(
        Database::connection()
    );

    $result = $reviewService->getForModeration(
        $validator->validated()
    );

    Response::json([
        'success' => true,
        'data' => $result,
    ]);

} catch (Throwable $e) {

    Response::json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 500);

}