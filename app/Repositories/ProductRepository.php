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

        print_r($data);

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
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $productId, array $data): bool
    {
    }

    public function delete(int $productId): bool
    {
        $sql = "
        UPDATE products
        SET deleted_at = NOW()
        WHERE id = :id";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $productId
        ]);

        return $statement->rowCount() > 0;

    }

    public function findById(int $productId): ?array
    {
        $sql = "SELECT * from products WHERE id = :id AND deleted_at IS NULL";

        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $productId
        ]);

        $product = $statement->fetch();

        return $product ?: null;
    }

    public function findBySlug(int $vendorId, string $slug): ?array
    {
        $sql = "SELECT * from products WHERE vendor_id = :vendor_id AND slug = :slug AND deleted_at IS NULL LIMIT 1";

        $statement = $this->prepare($sql);

        $statement->execute([
            'vendor_id' => $vendorId,
            'slug' => $slug,
        ]);

        $product = $statement->fetch();

        return $product ?: null;

    }

    public function findAll(): array
    {
        $sql = "SELECT * from products WHERE deleted_at IS NULL ORDER BY created_at DESC";

        $statement = $this->prepare($sql);

        $statement->execute();

        $products = $statement->fetchAll();

        return $products;


    }

    public function findByVendor(int $vendorId): array
    {
        $sql = "SELECT *
        FROM products
        WHERE vendor_id = :vendor_id
        AND deleted_at IS NULL
        ORDER BY created_at DESC";

        $statement = $this->prepare($sql);

        $statement->execute([
            'vendor_id' => $vendorId
        ]);

        $products = $statement->fetchAll();

        return $products;
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
}