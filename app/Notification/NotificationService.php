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

    /**
     * Notifies every admin that a new vendor application needs
     * review. Mirrors notifyAdminsOfNewOrder()'s pattern exactly.
     */
    public function notifyAdminsOfVendorRequest(
        int $requestId,
        string $storeName
    ): void {

        $adminIds = $this->userRepository->findIdsByRole(
            Role::ADMIN
        );

        if (empty($adminIds)) {
            return;
        }

        $this->notificationRepository->createForUsers(
            $adminIds,
            "New vendor application from \"{$storeName}\".",
            "/admin/vendor_requests.php?id={$requestId}"
        );
    }

    /**
     * Notifies an applicant that their vendor request was decided.
     * The approved link points at the congratulations page (which
     * also refreshes their session role), not the request itself -
     * once decided, the applicant has nothing more to review.
     */
    public function notifyUserOfVendorDecision(
        int $userId,
        bool $approved,
        ?string $reason
    ): void {

        $content = $approved
            ? 'Your vendor application was approved! Click to get started.'
            : "Your vendor application was not approved. Reason: {$reason}";

        $this->notificationRepository->create([
            'user_id' => $userId,
            'content' => $content,
            'url' => $approved ? '/vendor-approved.php' : '/become-vendor.php',
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