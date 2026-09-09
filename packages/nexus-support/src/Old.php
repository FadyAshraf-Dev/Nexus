<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Instantiable, dependency-free version of Nexus's old-input bag
 * (used to repopulate forms after a failed submission). Takes a
 * SessionStore via the constructor instead of touching $_SESSION
 * directly. The static app::class Old facade (app/Helpers/Old.php)
 * wraps one of these (backed by ArraySessionStore) behind the
 * original static API.
 */
final class Old
{
    private const SESSION_KEY = 'old';

    public function __construct(private readonly SessionStore $store)
    {
    }

    public function set(array $data): void
    {
        $this->store->set(self::SESSION_KEY, $data);
    }

    public function get(string $key, mixed $default = ''): mixed
    {
        $data = $this->store->get(self::SESSION_KEY, []);
        return $data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->store->get(self::SESSION_KEY, []);
    }

    public function clear(): void
    {
        $this->store->remove(self::SESSION_KEY);
    }
}
