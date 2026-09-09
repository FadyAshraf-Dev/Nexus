<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Default SessionStore backed by PHP's $_SESSION superglobal - this
 * is what Nexus's app-side facades (Errors, Old, Flash) construct
 * by default, so existing behavior in the live app is unchanged.
 */
final class ArraySessionStore implements SessionStore
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
}
