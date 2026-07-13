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
            if ($this->imageService->hasUploads($files)) {
                $this->imageService->uploadProductImages($productId, $files);
            }

            $this->pdo->commit();

            return $productId;
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            throw $e;

        }
    }
    public function getVendorProducts(
        int $vendorId,
        array $filters
    ): array {

        $totalProducts = $this->productRepository->countVendorProducts(
            $vendorId,
            $filters['search'],
            $filters['category_id'],
            $filters['status']
        );

        $pagination = $this->calculatePagination(
            $filters['page'],
            $filters['per_page'],
            $totalProducts
        );

        $products = $this->productRepository->findVendorProducts(
            $vendorId,
            $pagination['per_page'],
            $pagination['offset'],
            $filters['search'],
            $filters['category_id'],
            $filters['status'],
            $filters['sort_by'],
            $filters['sort_direction']
        );

        return [
            'products' => $products,
            'pagination' => $pagination,
            'filters' => $filters,
        ];
    }
    private function generateUniqueSlug(
        int $vendorId,
        string $productName,
        ?int $ignoreProductId = null

    ): string {

        $baseSlug = Html::slug($productName);

        $slug = $baseSlug;

        $counter = 1;

        while (

            $ignoreProductId === null

            ? $this->productRepository->slugExists(
                $vendorId,
                $slug
            )

            : $this->productRepository->slugExistsExcept(
                $vendorId,
                $slug,
                $ignoreProductId
            )

        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
    private function calculatePagination(
        int $page,
        int $perPage,
        int $totalProducts
    ): array {

        $totalPages = max(
            1,
            (int) ceil($totalProducts / $perPage)
        );

        $page = min(
            $page,
            $totalPages
        );

        $offset = ($page - 1) * $perPage;

        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'offset' => $offset,
            'total_products' => $totalProducts,
            'total_pages' => $totalPages,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }
}