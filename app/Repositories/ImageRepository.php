<?php
declare(strict_types=1);
final class ImageRepository extends Repository
{
    public function create(
        int $productId,
        string $imagePath,
        int $sortOrder
    ): int
    {
        $sql = "INSERT INTO `product_images`( 
            product_id, 
            image_path, 
            sort_order
        ) 
        VALUES (
            :product_id,
            :image_path,
            :sort_order
        )";
        $statement = $this->prepare($sql);

        $statement->execute([
            'product_id' => $productId,
            'image_path' => $imagePath,
            'sort_order' => $sortOrder,
        ]);
        return (int) $this->lastInsertId();
    }

    public function delete(int $imageId): bool
    {
        $sql = "DELETE FROM `product_images` 
        WHERE id = :id";
        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $imageId,
        ]);
        return $statement->rowCount() > 0;

    }

    public function deleteByProduct(int $productId): bool
    {
        $sql = "DELETE FROM `product_images` 
        WHERE product_id = :product_id";
        $statement = $this->prepare($sql);

        $statement->execute([
            'product_id' => $productId,
        ]);
        return $statement->rowCount() > 0;
    }

    public function findById(int $imageId): ?array
    {
        $sql = "SELECT * FROM `product_images` 
        WHERE id = :id
        LIMIT 1";
        $statement = $this->prepare($sql);

        $statement->execute([
            'id' => $imageId,
        ]);
        $image = $statement->fetch();

        return $image ?: null;

    }

    public function findByProduct(int $productId): array
    {
        $sql = "SELECT * FROM `product_images` 
        WHERE product_id = :product_id
        ORDER BY sort_order ASC";
        $statement = $this->prepare($sql);

        $statement->execute([
            'product_id' => $productId,
        ]);
        $images = $statement->fetchAll();

        return $images;

    }
}