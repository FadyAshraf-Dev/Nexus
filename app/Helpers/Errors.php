<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\ArraySessionStore;
use FadyAshrafDev\NexusSupport\Errors as ErrorsCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Errors.
 *
 * Every existing call site is untouched by this refactor. The
 * package's Errors class is backed by ArraySessionStore, which
 * reads/writes $_SESSION exactly as the original static class did.
 */
final class Errors
{
    private static ?ErrorsCore $instance = null;

    public static function set(array $errors): void
    {
        self::instance()->set($errors);
    }

    public static function get(string $key, mixed $default = ''): mixed
    {
        return self::instance()->get($key, $default);
    }

    public static function all(): array
    {
        return self::instance()->all();
    }

    public static function has(string $key): bool
    {
        return self::instance()->has($key);
    }

    public static function clear(): void
    {
        self::instance()->clear();
    }

    private static function instance(): ErrorsCore
    {
        return self::$instance ??= new ErrorsCore(new ArraySessionStore());
    }
}
