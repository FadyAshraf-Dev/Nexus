<?php
declare(strict_types=1);
final class Token
{
    private TokenRepository $tokenRepository;
    private UserRepository $userRepository;
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->tokenRepository = new TokenRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
    }
    public function create(
        int $userId,
    ): void {
        $this->issueToken($userId);

    }

    public function restore(): bool
    {
        $cookie = $this->readCookie();

        if ($cookie === null) {
            return false;
        }

        ['selector' => $selector, 'token' => $token] = $cookie;

        $rememberToken = $this->tokenRepository->findBySelector($selector);

        if ($rememberToken === null) {
            Cookie::delete(
                Config::app('cookies.name')
            );
            return false;
        }

        if (
            new DateTimeImmutable() >
            new DateTimeImmutable($rememberToken['expires_at'])
        ) {
            $this->destroy(
                (int) $rememberToken['user_id']
            );

            return false;
        }

        if (
            !hash_equals(
                $rememberToken['token_hash'],
                $this->hashToken($token)
            )
        ) {
            $this->destroy(
                (int) $rememberToken['user_id']
            );

            return false;
        }

        $user = $this->userRepository->findById(
            (int) $rememberToken['user_id']
        );

        if ($user === null) {
            $this->destroy(
                (int) $rememberToken['user_id']
            );

            return false;
        }

        $this->rotateToken(
            (int) $rememberToken['id']
        );

        Session::login($user);

        return true;
    }
    public function destroy(int $userId): void
    {
        $this->tokenRepository->deleteByUser($userId);

        Cookie::delete(
            Config::app('cookies.name')
        );
    }
    private function generateSelector(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
    private function issueToken(int $userId): void
    {
        $token = $this->generateToken();
        $selector = $this->generateSelector();
        $hashedToken = $this->hashToken($token);
        $expiresAt = new DateTimeImmutable(Config::app('cookies.duration'));
        $this->tokenRepository->create($userId, $selector, $hashedToken, $expiresAt);
        Cookie::set(
            Config::app('cookies.name'),
            "$selector:$token",
            $expiresAt->getTimestamp()
        );

    }

    private function readCookie(): ?array
    {
        $cookieName = Config::app('cookies.name');

        if (!isset($_COOKIE[$cookieName])) {
            return null;
        }

        $parts = explode(':', $_COOKIE[$cookieName], 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$selector, $token] = $parts;

        if ($selector === '' || $token === '') {
            return null;
        }

        return [
            'selector' => $selector,
            'token' => $token,
        ];
    }
    private function rotateToken(
        int $tokenId
    ): void {
        $selector = $this->generateSelector();

        $token = $this->generateToken();

        $hashedToken = $this->hashToken($token);

        $expiresAt = new DateTimeImmutable(
            Config::app('cookies.duration')
        );

        $this->tokenRepository->updateToken(
            $tokenId,
            $selector,
            $hashedToken,
            $expiresAt
        );

        Cookie::set(
            Config::app('cookies.name'),
            "$selector:$token",
            $expiresAt->getTimestamp()
        );
    }
}