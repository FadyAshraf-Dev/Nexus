<?php

declare(strict_types=1);

final class ReviewService
{
    private ReviewRepository $reviewRepository;
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;

    public function __construct(
        private readonly PDO $pdo
    ) {
        $this->reviewRepository = new ReviewRepository($pdo);
        $this->orderRepository = new OrderRepository($pdo);
        $this->productRepository = new ProductRepository($pdo);
    }

    /**
     * Checks whether a user is currently eligible to review a product:
     * they have a delivered order containing it, and haven't already
     * reviewed that specific purchase.
     */
    public function checkEligibility(int $userId, int $productId): array
    {
        $orderItemId = $this->orderRepository->findEligibleOrderItem(
            $userId,
            $productId
        );

        return [
            'eligible' => $orderItemId !== null,
            'order_item_id' => $orderItemId,
        ];
    }

    /**
     * Submits a review. Auto-approves on submit (see design discussion:
     * moderation is exception-based, not a gate on every submission) -
     * approved_at is set immediately rather than left null for an admin
     * queue.
     *
     * Re-validates eligibility here rather than trusting a client-
     * supplied order_item_id, for the same reason checkout re-validates
     * coupons server-side: the client's last-known state can be stale
     * or spoofed.
     */
    public function submit(
        int $userId,
        int $productId,
        int $rate,
        ?string $content
    ): int {

        $orderItemId = $this->orderRepository->findEligibleOrderItem(
            $userId,
            $productId
        );

        if ($orderItemId === null) {
            throw new RuntimeException(
                "You can only review products from delivered orders you haven't already reviewed."
            );
        }

        $this->pdo->beginTransaction();

        try {

            $reviewId = $this->reviewRepository->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'order_item_id' => $orderItemId,
                'rate' => $rate,
                'content' => $content,
                'approved_at' => date('Y-m-d H:i:s'),
            ]);

            $this->recalculateProductRating($productId);

            $this->pdo->commit();

            return $reviewId;

        } catch (Throwable $exception) {

            $this->pdo->rollBack();

            throw $exception;

        }

    }

    /**
     * Edits a user's own review. Re-approves automatically (same
     * auto-approve policy as submission) and recalculates the aggregate,
     * since the rate may have changed.
     */
    public function update(
        int $reviewId,
        int $userId,
        int $rate,
        ?string $content
    ): void {

        $review = $this->reviewRepository->findById($reviewId);

        if ($review === null || (int) $review['user_id'] !== $userId) {
            throw new RuntimeException("Review not found.");
        }

        $this->pdo->beginTransaction();

        try {

            $this->reviewRepository->update($reviewId, [
                'rate' => $rate,
                'content' => $content,
            ]);

            $this->recalculateProductRating((int) $review['product_id']);

            $this->pdo->commit();

        } catch (Throwable $exception) {

            $this->pdo->rollBack();

            throw $exception;

        }

    }

    /**
     * Soft-deletes a review. Used both for a user removing their own
     * review, and for admin moderation (removing content that violates
     * guidelines) - the caller is responsible for the authorization
     * check on WHO is allowed to call this for a given review.
     */
    public function remove(int $reviewId): void
    {
        $review = $this->reviewRepository->findById($reviewId);

        if ($review === null) {
            throw new RuntimeException("Review not found.");
        }

        $this->pdo->beginTransaction();

        try {

            $this->reviewRepository->softDelete($reviewId);

            $this->recalculateProductRating((int) $review['product_id']);

            $this->pdo->commit();

        } catch (Throwable $exception) {

            $this->pdo->rollBack();

            throw $exception;

        }

    }

    /**
     * Reverses a soft delete (admin moderation "unhide"). Recalculates
     * the product rating since a restored review counts toward the
     * aggregate again.
     */
    public function restore(int $reviewId): void
    {
        $review = $this->reviewRepository->findByIdIncludingDeleted($reviewId);

        if ($review === null) {
            throw new RuntimeException("Review not found.");
        }

        $this->pdo->beginTransaction();

        try {

            $this->reviewRepository->restore($reviewId);

            $this->recalculateProductRating((int) $review['product_id']);

            $this->pdo->commit();

        } catch (Throwable $exception) {

            $this->pdo->rollBack();

            throw $exception;

        }

    }

    /**
     * Paginated, filterable review listing for the admin moderation
     * table. Mirrors ProductService::getProducts' count-then-paginate-
     * then-fetch pattern.
     */
    public function getForModeration(array $filters): array
    {
        // NOTE on casting: `(int) $x ?? null` does NOT do what it looks
        // like - (int) binds tighter than ??, so it casts FIRST (turning
        // a missing/null value into 0 via an "undefined array key"
        // warning), and the ?? never gets a chance to apply since 0 is
        // never null. Extracting to locals first, with the null-coalesce
        // applied before any cast, avoids that trap.
        $productId = isset($filters['product_id']) && $filters['product_id'] !== ''
            ? (int) $filters['product_id']
            : null;

        $minRate = isset($filters['min_rate']) && $filters['min_rate'] !== ''
            ? (int) $filters['min_rate']
            : null;

        $search = $filters['search'] ?? null;
        $status = $filters['status'] ?? null;

        $page = isset($filters['page']) && $filters['page'] !== ''
            ? (int) $filters['page']
            : 1;

        $perPage = isset($filters['per_page']) && $filters['per_page'] !== ''
            ? (int) $filters['per_page']
            : 10;

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'DESC';

        $totalReviews = $this->reviewRepository->countForModeration(
            $productId,
            $minRate,
            $search,
            $status
        );

        $pagination = $this->calculatePagination(
            $page,
            $perPage,
            $totalReviews
        );

        $reviews = $this->reviewRepository->findForModeration(
            $pagination['per_page'],
            $pagination['offset'],
            $productId,
            $minRate,
            $search,
            $status,
            $sortBy,
            $sortDirection
        );

        return [
            'reviews' => $reviews,
            'pagination' => $pagination,
        ];

    }

    /**
     * Same pagination math as ProductService::calculatePagination -
     * duplicated here rather than shared, since the two services have
     * no other relationship and a shared PaginationService would be
     * premature abstraction for two call sites.
     */
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

    public function getForProduct(
        int $productId,
        int $limit = 20,
        int $offset = 0
    ): array {

        return $this->reviewRepository->findApprovedForProduct(
            $productId,
            $limit,
            $offset
        );

    }

    public function getUserReviewForProduct(
        int $userId,
        int $productId
    ): ?array {

        return $this->reviewRepository->findByUserAndProduct(
            $userId,
            $productId
        );

    }

    public function getById(int $reviewId): ?array
    {
        return $this->reviewRepository->findById($reviewId);
    }

    /**
     * THE single place products.avg_rating/review_count are written.
     * Every review mutation (submit/update/remove) calls this instead
     * of touching the aggregate columns directly, so the cached value
     * can never drift out of sync with the actual review rows.
     */
    private function recalculateProductRating(int $productId): void
    {
        $aggregate = $this->reviewRepository->getAggregateForProduct(
            $productId
        );

        $this->productRepository->updateRating(
            $productId,
            $aggregate['avg_rating'],
            $aggregate['review_count']
        );

    }
}