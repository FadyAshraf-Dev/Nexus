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

    /**
     * Paginated, filterable coupon listing for admin management.
     * Mirrors ReviewService::getForModeration's pattern, including the
     * careful filter extraction: `(int) $x ?? default` is NOT safe here
     * (cast binds tighter than ??, so it casts a missing/null value
     * before the coalesce ever runs) - values are checked with isset()
     * before any cast is applied.
     */
    public function getForListing(array $filters): array
    {
        $status = $filters['status'] ?? null;

        $page = isset($filters['page']) && $filters['page'] !== ''
            ? (int) $filters['page']
            : 1;

        $perPage = isset($filters['per_page']) && $filters['per_page'] !== ''
            ? (int) $filters['per_page']
            : 10;

        $totalCoupons = $this->couponRepository->countForListing($status);

        $pagination = $this->calculatePagination(
            $page,
            $perPage,
            $totalCoupons
        );

        $coupons = $this->couponRepository->findForListing(
            $pagination['per_page'],
            $pagination['offset'],
            $status
        );

        return [
            'coupons' => $coupons,
            'pagination' => $pagination,
        ];

    }

    /**
     * Toggles a coupon's active/inactive status - distinct from
     * deleteCoupon(), which soft-deletes it entirely. A coupon can be
     * temporarily disabled (status = inactive) without losing it.
     */
    public function toggleStatus(int $couponId): void
    {
        $coupon = $this->couponRepository->findById($couponId);

        if ($coupon === null) {
            throw new RuntimeException("Coupon not found.");
        }

        $newStatus = $coupon['status'] === 'active' ? 'inactive' : 'active';

        $this->couponRepository->updateStatus($couponId, $newStatus);

    }

    private function calculatePagination(
        int $page,
        int $perPage,
        int $totalItems
    ): array {

        $totalPages = max(
            1,
            (int) ceil($totalItems / $perPage)
        );

        $page = min($page, $totalPages);

        $offset = ($page - 1) * $perPage;

        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'offset' => $offset,
            'total_products' => $totalItems,
            'total_pages' => $totalPages,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];

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