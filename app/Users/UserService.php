<?php

declare(strict_types=1);

final class UserService
{
    private UserRepository $userRepository;
    private EmailVerification $emailVerification;
    private PasswordReset $passwordReset;

    public function __construct(PDO $pdo)
    {
        $this->userRepository = new UserRepository($pdo);
        $this->emailVerification = new EmailVerification($pdo);
        $this->passwordReset = new PasswordReset($pdo);
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

    /**
     * Updates the logged-in user's email + username (profile
     * page "edit info" form).
     *
     * If the email actually changed, the account is de-verified
     * and a fresh activation email goes out immediately -
     * otherwise someone could point their account at an email
     * they don't own without ever proving it, which defeats the
     * verification system entirely.
     *
     * @throws RuntimeException
     */
    public function updateProfile(
        int $userId,
        string $email,
        string $username
    ): void {

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new RuntimeException('User not found.');
        }

        $emailChanged = $email !== $user['email'];

        $usernameChanged = $username !== $user['username'];

        if ($emailChanged) {

            $existing = $this->userRepository->findByEmail($email);

            if ($existing !== null && (int) $existing['id'] !== $userId) {
                throw new RuntimeException(
                    'An account with that email already exists.'
                );
            }

        }

        if ($usernameChanged) {

            $existing = $this->userRepository->findByUsername($username);

            if ($existing !== null && (int) $existing['id'] !== $userId) {
                throw new RuntimeException(
                    'That username is already taken.'
                );
            }

        }

        $this->userRepository->updateProfile(
            $userId,
            $email,
            $username,
            $emailChanged
        );

        if ($emailChanged) {
            $this->emailVerification->issue($userId, $email);
        }
    }

    /**
     * Changes the logged-in user's password (profile page
     * "change password" form). Requires the current password,
     * unlike resetPassword() which is reached via an emailed
     * token instead.
     *
     * @throws RuntimeException
     */
    public function changePassword(
        int $userId,
        string $currentPassword,
        string $newPassword
    ): void {

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new RuntimeException('User not found.');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            throw new RuntimeException('Current password is incorrect.');
        }

        $this->userRepository->updatePassword(
            $userId,
            password_hash($newPassword, PASSWORD_DEFAULT)
        );
    }

    /**
     * Issues a password reset email for $email, if an account
     * with that email exists. Always returns silently either way
     * - the caller reports the same generic message regardless,
     * so this endpoint can't be used to enumerate registered
     * emails.
     */
    public function requestPasswordReset(string $email): void
    {
        $this->passwordReset->issue($email);
    }

    /**
     * Consumes a reset link's "selector:token" pair and sets the
     * account's password to $newPassword.
     *
     * @throws RuntimeException if the token is invalid, expired,
     *                          or already used.
     */
    public function resetPassword(
        string $selectorAndToken,
        string $newPassword
    ): void {

        $userId = $this->passwordReset->consume($selectorAndToken);

        if ($userId === null) {
            throw new RuntimeException(
                'This password reset link is invalid or has expired.'
            );
        }

        $this->userRepository->updatePassword(
            $userId,
            password_hash($newPassword, PASSWORD_DEFAULT)
        );
    }
}