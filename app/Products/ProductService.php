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

            throw new RuntimeException(
                'Unable to create product.',
                0,
                $e
            );

        }
    }

    public function updateProduct(
        int $productId,
        array $productData,
        array $files
    ): void {

        if ($message = ProductValidator::validateImageCount($files)) {
            throw new InvalidArgumentException($message);
        }

        try {

            $this->pdo->beginTransaction();

            $existingProduct = $this->productRepository->findById($productId);

            if (!$existingProduct) {
                throw new RuntimeException("Product not found.");
            }

            if ($existingProduct['product_name'] !== $productData['product_name']) {

                $productData['slug'] = $this->generateUniqueSlug(
                    $existingProduct['vendor_id'],
                    $productData['product_name'],
                    $productId,
                );
            } else {

                $productData['slug'] = $existingProduct['slug'];

            }

            $productData['discount_value'] ??= null;

            $this->productRepository->update(
                $productId,
                $productData
            );


            if ($this->imageService->hasUploads($files)) {

                $this->imageService->replaceProductImages($productId, $files);

            }
            $this->pdo->commit();

        } catch (Throwable $e) {

            $this->pdo->rollBack();

            throw $e;

        }
    }
    public function deleteProduct(int $productId): void
    {
        try {

            $this->pdo->beginTransaction();

            $product = $this->productRepository->findById($productId);

            if (!$product) {
                throw new RuntimeException('Product not found.');
            }

            $this->imageService->deleteProductImages($productId);

            if (!$this->productRepository->delete($productId)) {
                throw new RuntimeException('Unable to delete product.');
            }

            $this->pdo->commit();

        } catch (Throwable $e) {

            $this->pdo->rollBack();

            throw $e;

        }
    }
    public function getProducts(
        array $filters,
        ?int $vendorId = null
    ): array {

        $totalProducts = $this->productRepository->countProducts(
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

        $products = $this->productRepository->findProducts(
            $pagination['per_page'],
            $pagination['offset'],
            $vendorId,
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