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

    // NOTE: filter_input(INPUT_POST, ...) reads PHP's raw request-parse
    // buffer, populated only for real x-www-form-urlencoded/multipart
    // bodies. bootstrap.php decodes a JSON body by reassigning the
    // $_POST superglobal variable itself, which filter_input() never
    // sees - so for JSON requests (which this app always sends via
    // Ajax.js), filter_input(INPUT_POST, ...) always returns null even
    // when $_POST clearly has the key. Read $_POST directly instead.
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

    $validator = ReviewValidator::validateUpdate($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);

    }

    $validated = $validator->validated();

    $service = new ReviewService(Database::connection());

    // Ownership is checked inside ReviewService::update() itself
    // (compares the review's user_id against the id passed here) -
    // it throws if this user doesn't own the review.
    $service->update(
        $reviewId,
        Session::id(),
        (int) $validated['rate'],
        $validated['content'] ?? null
    );

    Response::json([
        'success' => true,
        'message' => 'Review updated.',
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