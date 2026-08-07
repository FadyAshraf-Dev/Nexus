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

    $couponId = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$couponId) {
        Response::json([
            'success' => false,
            'message' => 'Invalid coupon id.',
        ], 422);
    }

    $couponService = new CouponService(
        Database::connection()
    );

    $deleted = $couponService->deleteCoupon($couponId);

    if (!$deleted) {
        Response::json([
            'success' => false,
            'message' => 'Coupon not found.',
        ], 404);
    }

    Response::json([
        'success' => true,
        'message' => 'Coupon deleted.',
    ]);

} catch (Throwable $e) {

    error_log((string) $e);

    Response::json([
        'success' => false,
        'message' => 'An unexpected error occurred.',
    ], 500);

}