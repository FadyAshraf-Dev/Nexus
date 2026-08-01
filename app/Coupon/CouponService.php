<?php

declare(strict_types=1);



final class CouponService
{
    private CouponRepository $couponRepository;

    public function __construct(
        private readonly PDO $pdo
    ) {
        $this->couponRepository =
            new CouponRepository($pdo);
    }

    /**
     * Applies a coupon to the given subtotal.
     *
     * Returns the coupon and calculated discount.
     *
     * Validation is performed here so callers only
     * need a coupon code and subtotal.
     */
    public function applyCoupon(
        string $code,
        float $subtotal
    ): array {

        $coupon = $this->couponRepository
            ->findByCode(trim($code));

        if ($coupon === null) {
            throw new RuntimeException(
                "Coupon does not exist."
            );
        }

        $this->validateCoupon(
            $coupon,
            $subtotal
        );

        $discount = $this->calculateDiscount(
            $coupon,
            $subtotal
        );

        return [
            "coupon" => $coupon,
            "subtotal" => $subtotal,
            "discount" => $discount,
            "total" => max(0, $subtotal - $discount)
        ];
    }

    /**
     * Finds a coupon.
     */
    public function findCoupon(
        int $couponId
    ): ?array {

        return $this->couponRepository
            ->findById($couponId);
    }

    /**
     * Creates a coupon.
     */
    public function createCoupon(
        array $data
    ): int {

        return $this->couponRepository
            ->create($data);
    }

    /**
     * Updates a coupon.
     */
    public function updateCoupon(
        int $couponId,
        array $data
    ): bool {

        return $this->couponRepository
            ->update(
                $couponId,
                $data
            );
    }

    /**
     * Soft deletes a coupon.
     */
    public function deleteCoupon(
        int $couponId
    ): bool {

        return $this->couponRepository
            ->delete($couponId);
    }

    /**
     * Marks a coupon as used.
     */
    public function incrementUsage(
        int $couponId
    ): bool {

        return $this->couponRepository
            ->incrementUsage($couponId);
    }

    // --------------------------------------------------
    // Private Helpers
    // --------------------------------------------------

    /**
     * Validates coupon business rules.
     */
    private function validateCoupon(
        array $coupon,
        float $subtotal
    ): void {

        if ($coupon["status"] !== "active") {
            throw new RuntimeException(
                "Coupon is inactive."
            );
        }

        $now = new DateTime();

        if (
            $coupon["starts_at"] !== null &&
            new DateTime($coupon["starts_at"]) > $now
        ) {
            throw new RuntimeException(
                "Coupon is not active yet."
            );
        }

        if (
            $coupon["expires_at"] !== null &&
            new DateTime($coupon["expires_at"]) < $now
        ) {
            throw new RuntimeException(
                "Coupon has expired."
            );
        }

        if (
            $coupon["usage_limit"] !== null &&
            (int) $coupon["used_count"] >=
            (int) $coupon["usage_limit"]
        ) {
            throw new RuntimeException(
                "Coupon usage limit reached."
            );
        }

        if (
            $coupon["minimum_order"] !== null &&
            $subtotal < (float) $coupon["minimum_order"]
        ) {
            throw new RuntimeException(
                "Minimum order amount not met."
            );
        }
    }

    /**
     * Calculates the discount.
     */
    private function calculateDiscount(
        array $coupon,
        float $subtotal
    ): float {

        $discount = 0.0;

        if ($coupon["type"] === "fixed") {

            $discount =
                (float) $coupon["value"];

        } else {

            $discount =
                $subtotal *
                ((float) $coupon["value"] / 100);
        }

        if (
            $coupon["maximum_discount"] !== null
        ) {
            $discount = min(
                $discount,
                (float) $coupon["maximum_discount"]
            );
        }

        return min(
            $discount,
            $subtotal
        );
    }
}