<?php

declare(strict_types=1);

final class VendorRequestService
{
    private VendorRequestRepository $vendorRequestRepository;
    private UserRepository $userRepository;
    private NotificationService $notificationService;

    public function __construct(
        private readonly PDO $pdo
    ) {

        $this->vendorRequestRepository =
            new VendorRequestRepository($pdo);

        $this->userRepository =
            new UserRepository($pdo);

        $this->notificationService =
            new NotificationService($pdo);
    }

    /**
     * Submits a vendor application for $userId. Blocks a second
     * submission while one is already pending - approved/rejected
     * history stays (a rejected applicant can re-apply), only
     * "pending" is meant to be singular at a time.
     *
     * @throws RuntimeException if a pending request already exists.
     */
    public function apply(int $userId, array $data): int
    {
        $existing = $this->vendorRequestRepository
            ->findPendingForUser($userId);

        if ($existing !== null) {
            throw new RuntimeException(
                'You already have a vendor application pending review.'
            );
        }

        $requestId = $this->vendorRequestRepository->create([
            'user_id' => $userId,
            'store_name' => $data['store_name'],
            'description' => $data['description'],
            'phone' => $data['phone'],
        ]);

        $this->notificationService->notifyAdminsOfVendorRequest(
            $requestId,
            $data['store_name']
        );

        return $requestId;
    }

    /**
     * Approves a pending request: flips the applicant's role to
     * VENDOR and marks the request approved, as a single
     * transaction so the two writes can't land only one at a time.
     * Notifies the applicant both in-app and by email, since email
     * is the only channel guaranteed to actually reach them (the
     * in-app bell only fires on their next page load).
     *
     * @throws RuntimeException if the request doesn't exist or
     *                          isn't pending.
     */
    public function approve(int $requestId, int $reviewerId): void
    {
        $request = $this->getPendingOrFail($requestId);

        $this->pdo->beginTransaction();

        try {

            $this->vendorRequestRepository->approve(
                $requestId,
                $reviewerId
            );

            $this->userRepository->updateRole(
                (int) $request['user_id'],
                Role::VENDOR
            );

            $this->pdo->commit();

        } catch (Throwable $e) {

            $this->pdo->rollBack();

            throw $e;

        }

        $this->notifyApplicantOfDecision(
            (int) $request['user_id'],
            true,
            null
        );
    }

    /**
     * Rejects a pending request with a reason. Does not touch the
     * applicant's role (they were never upgraded), just records
     * the decision and notifies them.
     *
     * @throws RuntimeException if the request doesn't exist or
     *                          isn't pending.
     */
    public function reject(
        int $requestId,
        int $reviewerId,
        string $reason
    ): void {

        $request = $this->getPendingOrFail($requestId);

        $this->vendorRequestRepository->reject(
            $requestId,
            $reviewerId,
            $reason
        );

        $this->notifyApplicantOfDecision(
            (int) $request['user_id'],
            false,
            $reason
        );
    }

    private function getPendingOrFail(int $requestId): array
    {
        $request = $this->vendorRequestRepository->findById($requestId);

        if ($request === null) {
            throw new RuntimeException('Vendor request not found.');
        }

        if ($request['status'] !== 'pending') {
            throw new RuntimeException(
                'This request has already been reviewed.'
            );
        }

        return $request;
    }

    private function notifyApplicantOfDecision(
        int $userId,
        bool $approved,
        ?string $reason
    ): void {

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            return;
        }

        $this->notificationService->notifyUserOfVendorDecision(
            $userId,
            $approved,
            $reason
        );

        $this->sendDecisionEmail($user['email'], $approved, $reason);
    }

    private function sendDecisionEmail(
        string $email,
        bool $approved,
        ?string $reason
    ): void {

        if ($approved) {

            $link = Mailer::absoluteUrl('vendor-approved.php');

            $subject = 'Your Nexus vendor application was approved!';

            $body = "Congratulations!\r\n\r\n"
                . "Your application to become a vendor on Nexus has been approved. "
                . "You can now create and manage your own product listings.\r\n\r\n"
                . "Get started here:\r\n{$link}\r\n";

        } else {

            $subject = 'An update on your Nexus vendor application';

            $body = "Hi,\r\n\r\n"
                . "Your application to become a vendor on Nexus was not approved this time.\r\n\r\n"
                . "Reason: {$reason}\r\n\r\n"
                . "You're welcome to submit a new application at any time.\r\n";

        }

        Mailer::send($email, $subject, $body);
    }
}
