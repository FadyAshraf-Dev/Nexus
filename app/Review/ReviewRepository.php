<?php

declare(strict_types=1);

final class ReviewRepository extends Repository
{
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO reviews
            (
                user_id,
                product_id,
                order_item_id,
                rate,
                content,
                approved_at
            )
            VALUES
            (
                :user_id,
                :product_id,
                :order_item_id,
                :rate,
                :content,
                :approved_at
            )
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id'       => $data['user_id'],
            'product_id'    => $data['product_id'],
            'order_item_id' => $data['order_item_id'],
            'rate'          => $data['rate'],
            'content'       => $data['content'] ?? null,
            'approved_at'   => $data['approved_at'] ?? null,
        ]);

        return $this->lastInsertId();
    }

    public function findById(int $reviewId): ?array
    {
        $sql = "
            SELECT *
            FROM reviews
            WHERE id = :id
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $reviewId,
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Same as findById but ignores deleted_at - needed for admin
     * moderation actions (like restore) that specifically operate on
     * reviews regardless of their current visibility state.
     */
    public function findByIdIncludingDeleted(int $reviewId): ?array
    {
        $sql = "
            SELECT *
            FROM reviews
            WHERE id = :id
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $reviewId,
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Approved, non-deleted reviews for a product, most recent first -
     * what the product page actually displays.
     */
    public function findApprovedForProduct(
        int $productId,
        int $limit = 20,
        int $offset = 0
    ): array {

        $sql = "
            SELECT
                r.*,
                COALESCE(u.username, 'Verified Buyer') AS reviewer_name

            FROM reviews r

            INNER JOIN users u
                ON u.id = r.user_id

            WHERE r.product_id = :product_id
              AND r.approved_at IS NOT NULL
              AND r.deleted_at IS NULL

            ORDER BY r.created_at DESC

            LIMIT :limit OFFSET :offset
        ";

        $statement = $this->prepare($sql);

        $statement->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * A user's own review for a product (any status - approved or not,
     * since a user should be able to see/edit their own pending review).
     */
    public function findByUserAndProduct(
        int $userId,
        int $productId
    ): ?array {

        $sql = "
            SELECT *
            FROM reviews
            WHERE user_id = :user_id
              AND product_id = :product_id
              AND deleted_at IS NULL
            LIMIT 1
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * The aggregate this app actually cares about: average rate and
     * count of currently-approved, non-deleted reviews for a product.
     * Used only by ReviewService's centralized recompute method - never
     * called for display, since products.avg_rating/review_count are
     * the cached read path for that.
     */
    public function getAggregateForProduct(int $productId): array
    {
        $sql = "
            SELECT
                COALESCE(AVG(rate), 0) AS avg_rating,
                COUNT(*) AS review_count

            FROM reviews

            WHERE product_id = :product_id
              AND approved_at IS NOT NULL
              AND deleted_at IS NULL
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'product_id' => $productId,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return [
            'avg_rating' => round((float) $row['avg_rating'], 2),
            'review_count' => (int) $row['review_count'],
        ];
    }

    public function softDelete(int $reviewId): bool
    {
        $sql = "
            UPDATE reviews
            SET deleted_at = NOW()
            WHERE id = :id
              AND deleted_at IS NULL
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $reviewId,
        ]);

        return $statement->rowCount() === 1;
    }

    /**
     * Reverses a soft delete - used by admin moderation to unhide a
     * review. Only succeeds if the review is currently deleted (no-op
     * on an already-active review).
     */
    public function restore(int $reviewId): bool
    {
        $sql = "
            UPDATE reviews
            SET deleted_at = NULL
            WHERE id = :id
              AND deleted_at IS NOT NULL
        ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $reviewId,
        ]);

        return $statement->rowCount() === 1;
    }

    /**
     * Counts reviews matching moderation filters. Unlike
     * findApprovedForProduct (storefront-facing, only approved/active
     * reviews), this intentionally has NO deleted_at filter by default -
     * admin moderation needs to see hidden reviews too, in order to
     * restore them. $status narrows to 'active' or 'hidden' explicitly
     * when the admin filters by it.
     */
    public function countForModeration(
        ?int $productId = null,
        ?int $minRate = null,
        ?string $reviewerSearch = null,
        ?string $status = null
    ): int {

        $sql = "
            SELECT COUNT(*) AS total
            FROM reviews r
            INNER JOIN users u ON u.id = r.user_id
            WHERE 1 = 1
        ";

        $parameters = [];

        $this->applyModerationFilters(
            $sql,
            $parameters,
            $productId,
            $minRate,
            $reviewerSearch,
            $status
        );

        $statement = $this->prepare($sql);
        $statement->execute($parameters);

        return (int) $statement->fetchColumn();
    }

    /**
     * Paginated, filterable review listing for admin moderation -
     * includes product name and reviewer username/email for display
     * context, and reviews of any status (active or soft-deleted).
     */
    public function findForModeration(
        int $limit,
        int $offset,
        ?int $productId = null,
        ?int $minRate = null,
        ?string $reviewerSearch = null,
        ?string $status = null,
        string $sortBy = 'created_at',
        string $sortDirection = 'DESC'
    ): array {

        $sortDirection = strtoupper($sortDirection);

        if (!in_array($sortDirection, ['ASC', 'DESC'], true)) {
            $sortDirection = 'DESC';
        }

        // Whitelisted against ReviewValidator::validateListing's `in:`
        // rule before ever reaching here - safe to interpolate directly,
        // same pattern as ProductRepository::findProducts.
        $allowedSortColumns = ['created_at', 'rate', 'updated_at'];

        if (!in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'created_at';
        }

        $sql = "
            SELECT
                r.id,
                r.user_id,
                r.product_id,
                r.rate,
                r.content,
                r.approved_at,
                r.created_at,
                r.deleted_at,
                COALESCE(u.username, u.email) AS reviewer_name,
                p.product_name

            FROM reviews r

            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN products p ON p.id = r.product_id

            WHERE 1 = 1
        ";

        $parameters = [];

        $this->applyModerationFilters(
            $sql,
            $parameters,
            $productId,
            $minRate,
            $reviewerSearch,
            $status
        );

        $sql .= "
            ORDER BY r.{$sortBy} {$sortDirection}
            LIMIT :limit
            OFFSET :offset
        ";

        $statement = $this->prepare($sql);

        foreach ($parameters as $key => $value) {
            $statement->bindValue(":{$key}", $value);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Shared WHERE-clause builder for countForModeration/findForModeration,
     * so the count and the actual page always agree on what "matches
     * the filters" means. Mirrors ProductRepository::buildProductFilters'
     * pattern of mutating $sql/$parameters by reference.
     */
    private function applyModerationFilters(
        string &$sql,
        array &$parameters,
        ?int $productId,
        ?int $minRate,
        ?string $reviewerSearch,
        ?string $status
    ): void {

        if ($productId !== null) {
            $sql .= " AND r.product_id = :product_id";
            $parameters['product_id'] = $productId;
        }

        if ($minRate !== null) {
            $sql .= " AND r.rate >= :min_rate";
            $parameters['min_rate'] = $minRate;
        }

        if ($reviewerSearch !== null && $reviewerSearch !== '') {
            $sql .= " AND (u.username LIKE :reviewer_search_username OR u.email LIKE :reviewer_search_email)";
            $parameters['reviewer_search_username'] = "%{$reviewerSearch}%";
            $parameters['reviewer_search_email'] = "%{$reviewerSearch}%";
        }

        if ($status === 'active') {
            $sql .= " AND r.deleted_at IS NULL";
        } elseif ($status === 'hidden') {
            $sql .= " AND r.deleted_at IS NOT NULL";
        }
        // $status === null (or any other value): no filter, show both.

    }

    public function update(int $reviewId, array $data): bool
    {
        $sql = "
            UPDATE reviews
            SET
                rate = :rate,
                content = :content
            WHERE id = :id
              AND deleted_at IS NULL
        ";

        $statement = $this->prepare($sql);

        return $statement->execute([
            'id' => $reviewId,
            'rate' => $data['rate'],
            'content' => $data['content'] ?? null,
        ]);
    }
}