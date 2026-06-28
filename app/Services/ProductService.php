<?php

declare(strict_types=1);


class ProductService
{
    private PDO $pdo;

    private ProductRepository $productRepository;

    private ImageService $imageService;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->productRepository = new ProductRepository($pdo);

        $this->imageService = new ImageService($pdo);
    }
    public function createProduct(
        array $productData,
        array $files
    ): int {
        try {
            $this->pdo->beginTransaction();
            $slug = $this->generateUniqueSlug($productData["vendor_id"], $productData["product_name"]);
            $productData["slug"] = $slug;

            $productData['discount_value'] ??= null;
            $productId = $this->productRepository->create($productData);
            if (
                isset($files['name']) &&
                !empty($files['name'])
            ) {
                $this->imageService->uploadProductImages($productId, $files);
            }
            echo 5;

            $this->pdo->commit();

            return $productId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            die("Unable to add product. " . $e);
        }
    }
    private function generateUniqueSlug(
        int $vendorId,
        string $productName
    ): string {

        $baseSlug = Html::slug($productName);

        $slug = $baseSlug;

        $counter = 1;

        while (
            $this->productRepository->slugExists(
                $vendorId,
                $slug
            )
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}