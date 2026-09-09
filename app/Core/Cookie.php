<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Cookie as CookieCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Cookie.
 * Every existing call site is untouched - identical behavior.
 */
final class Cookie
{
    public static function set(string $name, string $value, int $expires): void
    {
        CookieCore::set($name, $value, $expires);
    }

    public static function get(string $name): ?string
    {
        return CookieCore::get($name);
    }

    public static function exists(string $name): bool
    {
        return CookieCore::exists($name);
    }

    public static function delete(string $name): void
    {
        CookieCore::delete($name);
    }
}
