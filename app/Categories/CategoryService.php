<?php

declare(strict_types=1);

final class CategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct(PDO $pdo)
    {
        $this->categoryRepository = new CategoryRepository($pdo);
    }

    public function getCategories(array $filters): array
    {
        $totalCategories = $this->categoryRepository->countCategories();

        $pagination = $this->calculatePagination(
            $filters['page'],
            $filters['per_page'],
            $totalCategories
        );

        $categories = $this->categoryRepository->findCategories(
            $pagination['per_page'],
            $pagination['offset'],
            $filters['sort_by'],
            $filters['sort_direction']
        );

        return [
            'categories' => $categories,
            'pagination' => $pagination,
            'filters' => $filters,
        ];
    }

    private function calculatePagination(
        int $page,
        int $perPage,
        int $totalCategories
    ): array {

        $totalPages = max(
            1,
            (int) ceil($totalCategories / $perPage)
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
            'total_products' => $totalCategories,
            'total_pages' => $totalPages,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = $this->categoryRepository->findById($categoryId);

        if (!$category) {
            throw new RuntimeException('Category not found.');
        }

        try {

            if (!$this->categoryRepository->delete($categoryId)) {
                throw new RuntimeException('Unable to delete category.');
            }

        } catch (PDOException $e) {

            // FK violation (products.category_id references this row)
            // SQLSTATE 23000 is the integrity-constraint-violation class.
            if ($e->getCode() === '23000') {
                throw new RuntimeException(
                    'This category cannot be deleted because it still has products assigned to it.'
                );
            }

            throw $e;

        }
    }
}