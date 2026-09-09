<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\ArraySessionStore;
use FadyAshrafDev\NexusSupport\Flash as FlashCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Flash.
 *
 * Every existing call site is untouched by this refactor. The
 * package's Flash class is backed by ArraySessionStore, which
 * reads/writes $_SESSION exactly as the original static class did.
 */
class Flash
{
    private static ?FlashCore $instance = null;

    public static function add(string $type, string $message): void
    {
        self::instance()->add($type, $message);
    }

    public static function success(string $message): void
    {
        self::instance()->success($message);
    }

    public static function error(string $message): void
    {
        self::instance()->error($message);
    }

    public static function warning(string $message): void
    {
        self::instance()->warning($message);
    }

    public static function info(string $message): void
    {
        self::instance()->info($message);
    }

    public static function get(): array
    {
        return self::instance()->get();
    }

    public static function has(): bool
    {
        return self::instance()->has();
    }

    public static function display(): string
    {
        return self::instance()->display();
    }

    private static function instance(): FlashCore
    {
        return self::$instance ??= new FlashCore(new ArraySessionStore());
    }
}
