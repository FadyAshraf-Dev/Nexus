<?php

declare(strict_types=1);

final class UserService
{
    private UserRepository $userRepository;

    public function __construct(PDO $pdo)
    {
        $this->userRepository = new UserRepository($pdo);
    }

    public function getUsers(array $filters): array
    {
        $totalUsers = $this->userRepository->countUsers(
            $filters['search'],
            $filters['role_id']
        );

        $pagination = $this->calculatePagination(
            $filters['page'],
            $filters['per_page'],
            $totalUsers
        );

        $users = $this->userRepository->findUsers(
            $pagination['per_page'],
            $pagination['offset'],
            $filters['search'],
            $filters['role_id'],
            $filters['sort_by'],
            $filters['sort_direction']
        );

        return [
            'users' => $users,
            'pagination' => $pagination,
            'filters' => $filters,
        ];
    }

    private function calculatePagination(
        int $page,
        int $perPage,
        int $totalUsers
    ): array {

        $totalPages = max(
            1,
            (int) ceil($totalUsers / $perPage)
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
            'total_products' => $totalUsers,
            'total_pages' => $totalPages,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === Session::id()) {
            throw new RuntimeException('You cannot delete your own account.');
        }

        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new RuntimeException('User not found.');
        }

        try {

            if (!$this->userRepository->delete($userId)) {
                throw new RuntimeException('Unable to delete user.');
            }

        } catch (PDOException $e) {

            // FK violation - user still referenced by orders, products,
            // reviews, etc. SQLSTATE 23000 is the integrity-constraint
            // violation class (same handling as CategoryService::deleteCategory).
            if ($e->getCode() === '23000') {
                throw new RuntimeException(
                    'This user cannot be deleted because they have existing orders, products, or reviews linked to their account.'
                );
            }

            throw $e;

        }
    }
}