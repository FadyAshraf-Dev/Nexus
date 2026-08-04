<?php

declare(strict_types=1);

final class NotificationService
{
    private NotificationRepository $notificationRepository;
    private UserRepository $userRepository;

    public function __construct(
        private readonly PDO $pdo
    ) {
        $this->notificationRepository =
            new NotificationRepository($pdo);

        $this->userRepository =
            new UserRepository($pdo);
    }

    /**
     * Notifies every admin that a new order was placed.
     */
    public function notifyAdminsOfNewOrder(int $orderId): void
    {
        $adminIds = $this->userRepository->findIdsByRole(
            Role::ADMIN
        );

        if (empty($adminIds)) {
            return;
        }

        $this->notificationRepository->createForUsers(
            $adminIds,
            "A new order (#{$orderId}) has been placed.",
            "/admin/orders.php?id={$orderId}"
        );
    }

    /**
     * Notifies a product's vendor that it has newly dropped to or below
     * its low_stock_threshold.
     */
    public function notifyVendorOfLowStock(array $product): void
    {
        $this->notificationRepository->create([
            'user_id' => (int) $product['vendor_id'],
            'content' => "{$product['product_name']} is running low on stock.",
            'url'     => "/admin/products/edit.php?id={$product['id']}",
        ]);
    }

    public function getForUser(int $userId, int $limit = 10): array
    {
        return $this->notificationRepository->findForUser($userId, $limit);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->countUnread($userId);
    }

    public function markAllRead(int $userId): bool
    {
        return $this->notificationRepository->markAllRead($userId);
    }
}