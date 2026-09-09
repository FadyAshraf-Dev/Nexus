<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Instantiable, dependency-free version of Nexus's validation-error
 * bag. Takes a SessionStore via the constructor instead of touching
 * $_SESSION directly. The static app::class Errors facade
 * (app/Helpers/Errors.php) wraps one of these (backed by
 * ArraySessionStore) behind the original static API.
 */
final class Errors
{
    private const SESSION_KEY = 'errors';

    public function __construct(private readonly SessionStore $store)
    {
    }

    public function set(array $errors): void
    {
        $this->store->set(self::SESSION_KEY, $errors);
    }

    public function get(string $key, mixed $default = ''): mixed
    {
        $errors = $this->store->get(self::SESSION_KEY, []);
        return $errors[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->store->get(self::SESSION_KEY, []);
    }

    public function has(string $key): bool
    {
        $errors = $this->store->get(self::SESSION_KEY, []);
        return isset($errors[$key]);
    }

    public function clear(): void
    {
        $this->store->remove(self::SESSION_KEY);
    }
}
