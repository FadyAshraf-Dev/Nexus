<?php

declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/bootstrap/bootstrap.php';

Gatekeeper::authorize([1, 2, 3]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    Response::json([
        'success' => false,
        'message' => 'Method Not Allowed.'
    ], 405);
}

try {

    $validator = CouponValidator::validateApply($_POST);

    if ($validator->fails()) {

        Response::json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $validated = $validator->validated();

    $couponService = new CouponService(
        Database::connection()
    );

    $result = $couponService->applyCoupon(
        $validated['code'],
        $validated['subtotal']
    );

    Response::json([
        'success' => true,
        'data' => $result,
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
        'message' => $exception->getMessage(),
    ], 500);
}