<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\ArraySessionStore;
use FadyAshrafDev\NexusSupport\Old as OldCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Old.
 *
 * Every existing call site is untouched by this refactor. The
 * package's Old class is backed by ArraySessionStore, which
 * reads/writes $_SESSION exactly as the original static class did.
 */
final class Old
{
    private static ?OldCore $instance = null;

    public static function set(array $data): void
    {
        self::instance()->set($data);
    }

    public static function get(string $key, mixed $default = ''): mixed
    {
        return self::instance()->get($key, $default);
    }

    public static function all(): array
    {
        return self::instance()->all();
    }

    public static function clear(): void
    {
        self::instance()->clear();
    }

    private static function instance(): OldCore
    {
        return self::$instance ??= new OldCore(new ArraySessionStore());
    }
}
