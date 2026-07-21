<?php
declare(strict_types=1);
final class ProductRepository extends Repository
{
    public function create(array $data): int
    {

        $sql = "INSERT INTO products
        (
            product_name,
            vendor_id,
            slug,
            short_description,
            full_description,
            status,
            category_id,
            brand,
            cost_price,
            selling_price,
            discount_type,
            discount_value,
            stock_quantity,
            low_stock_threshold
        )
        VALUES
        (
            :product_name,
            :vendor_id,
            :slug,
            :short_description,
            :full_description,
            :status,
            :category_id,
            :brand,
            :cost_price,
            :selling_price,
            :discount_type,
            :discount_value,
            :stock_quantity,
            :low_stock_threshold
        )";

        $statement = $this->prepare($sql);


        $statement->execute([
            'product_name' => $data['product_name'],
            'vendor_id' => $data['vendor_id'],
            'slug' => $data['slug'],
            'short_description' => $data['short_description'],
            'full_description' => $data['full_description'],
            'status' => $data['status'],
            'category_id' => $data['category_id'],
            'brand' => $data['brand'],
            'cost_price' => $data['cost_price'],
            'selling_price' => $data['selling_price'],
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'stock_quantity' => $data['stock_quantity'],
            'low_stock_threshold' => $data['low_stock_threshold'],
        ]);
        return $this->lastInsertId();
    }

    public function update(
        int $productId,
        array $productData
    ): void {

        $sql = "
            UPDATE products SET
            product_name = :product_name,
            short_description = :short_description,
            full_description = :full_description,
            category_id = :category_id,
            slug = :slug,
            cost_price = :cost_price,
            selling_price = :selling_price,
            stock_quantity = :stock_quantity,
            low_stock_threshold = :low_stock_threshold,
            status = :status,
            discount_type = :discount_type,
            discount_value = :discount_value
        WHERE id = :id
    ";

        $productData['id'] = $productId;

        $statement = $this->pdo->prepare($sql);
        $statement->execute($productData);
    }
    public function delete(int $productId): bool
    {
        $sql = "
            UPDATE products
            SET deleted_at = NOW()
            WHERE id = :id
            AND deleted_at IS NULL
";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $productId
        ]);

        return $statement->rowCount() === 1;

    }

    public function findById(int $productId): ?array
    {
        $sql = "SELECT * from products WHERE id = :id AND deleted_at IS NULL Limit 1";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $productId
        ]);

        $product = $statement->fetch();

        return $product ?: null;
    }
    public function findRelatedProducts(
        int $categoryId,
        int $excludeProductId,
        int $limit = 4
    ): array {

        $sql = "
        SELECT
            p.id,
            p.slug,
            p.product_name,
            p.selling_price,
            p.discount_type,
            p.discount_value,
            (
                SELECT image_path
                FROM product_images
                WHERE product_id = p.id
                ORDER BY sort_order
                LIMIT 1
            ) AS image_path
        FROM products p
        WHERE
            p.category_id = :category_id
            AND p.id != :exclude_product_id
            AND p.status = 'active'
        ORDER BY RAND()
        LIMIT :limit
    ";

        $statement = $this->pdo->prepare($sql);

        $statement->bindValue(
            ':category_id',
            $categoryId,
            PDO::PARAM_INT
        );

        $statement->bindValue(
            ':exclude_product_id',
            $excludeProductId,
            PDO::PARAM_INT
        );

        $statement->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $statement->execute();

        return $statement->fetchAll();
    }
    public function findBySlug(
        string $slug,
        ?int $vendorId = null
    ): ?array {

        $sql = "
        SELECT
            p.id,
            p.slug,
            p.product_name,
            p.short_description,
            p.full_description,
            p.selling_price,
            p.discount_type,
            p.discount_value,
            p.stock_quantity,
            p.brand,
            p.category_id,
            c.category_name
        FROM products p
        INNER JOIN categories c
            ON c.id = p.category_id
        WHERE
            p.slug = :slug
            AND p.deleted_at IS NULL
    ";

        $parameters = [
            'slug' => $slug,
        ];

        if ($vendorId !== null) {

            $sql .= " AND p.vendor_id = :vendor_id";

            $parameters['vendor_id'] = $vendorId;

        } else {

            $sql .= " AND p.status = 'active'";

        }

        $sql .= " LIMIT 1";

        $statement = $this->prepare($sql);

        $statement->execute($parameters);

        return $statement->fetch() ?: null;
    }
    public function findProducts(
        int $limit,
        int $offset,
        ?int $vendorId = null,
        ?string $search = null,
        ?int $categoryId = null,
        ?string $status = null,
        string $sortBy = 'created_at',
        string $sortDirection = 'DESC',
    ): array {



        $sortDirection = strtoupper($sortDirection);

        if (!in_array($sortDirection, ['ASC', 'DESC'], true)) {
            $sortDirection = 'DESC';
        }

        $sql = "
    SELECT
        p.id,
        p.product_name,
        p.slug,
        p.brand,
        p.selling_price,
        p.discount_type,
        p.discount_value,
        p.stock_quantity,
        p.status,
        p.created_at,
        c.category_name,
        (
            SELECT image_path
            FROM product_images
            WHERE product_id = p.id
            ORDER BY sort_order ASC
            LIMIT 1
        ) AS image_path
    FROM products p
    INNER JOIN categories c
        ON c.id = p.category_id
    WHERE
        p.deleted_at IS NULL
";

        $parameters = [];

        $this->buildProductFilters(
            $sql,
            $parameters,
            $search,
            $categoryId,
            $status,
            $vendorId
        );
        $sql .= "
        ORDER BY p.{$sortBy} {$sortDirection}
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

        return $statement->fetchAll();
    }
    public function countProducts(
        ?int $vendorId = null,
        ?string $search = null,
        ?int $categoryId = null,
        ?string $status = null
    ): int {

        $sql = "
        SELECT COUNT(*)
        FROM products
        WHERE deleted_at IS NULL
    ";

        $parameters = [];

        $this->buildProductFilters(
            $sql,
            $parameters,
            $search,
            $categoryId,
            $status,
            $vendorId
        );

        $statement = $this->prepare($sql);

        $statement->execute($parameters);

        return (int) $statement->fetchColumn();
    }
    public function findVendorProductById(
        int $vendorId,
        int $productId
    ): ?array {

        $sql = "
        SELECT
            p.*,
            c.category_name
        FROM products p
        INNER JOIN categories c
            ON c.id = p.category_id
        WHERE
            p.id = :product_id
            AND p.vendor_id = :vendor_id
            AND p.deleted_at IS NULL
        LIMIT 1
    ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'product_id' => $productId,
            'vendor_id' => $vendorId,
        ]);

        $product = $statement->fetch();

        return $product ?: null;
    }
    public function slugExists(int $vendorId, string $slug): bool
    {
        $sql = "SELECT 1
        FROM products
        WHERE vendor_id = :vendor_id
        AND slug = :slug
        AND deleted_at IS NULL
        LIMIT 1";

        $statement = $this->prepare($sql);
        $statement->execute([
            'vendor_id' => $vendorId,
            'slug' => $slug,
        ]);

        return (bool) $statement->fetchColumn();
    }
    public function slugExistsExcept(
        int $vendorId,
        string $slug,
        int $productId
    ): bool {
        $sql = "
        SELECT 1
        FROM products
        WHERE vendor_id = :vendor_id
          AND slug = :slug
          AND id <> :product_id
          AND deleted_at IS NULL
        LIMIT 1
    ";

        $statement = $this->prepare($sql);

        $statement->execute([
            'vendor_id' => $vendorId,
            'slug' => $slug,
            'product_id' => $productId
        ]);

        return (bool) $statement->fetchColumn();
    }
    private function buildProductFilters(
        string &$sql,
        array &$parameters,
        ?string $search,
        ?int $categoryId,
        ?string $status,
        ?int $vendorId = null

    ): void {
        if ($vendorId !== null) {
            $sql .= "AND vendor_id = :vendor_id";
            $parameters['vendor_id'] = $vendorId;
        }
        if ($search !== null && $search !== '') {
            $sql .= " AND product_name LIKE :search";
            $parameters['search'] = '%' . $search . '%';
        }

        if ($categoryId !== null) {
            $sql .= " AND category_id = :category_id";
            $parameters['category_id'] = $categoryId;
        }

        if ($status !== null) {
            $sql .= " AND status = :status";
            $parameters['status'] = $status;
        }

    }
}