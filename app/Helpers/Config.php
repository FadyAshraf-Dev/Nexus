<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Config as ConfigCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Config.
 *
 * Every existing call site in this app (Config::app(...),
 * Config::database(...)) is untouched by the option-2 refactor -
 * this class still exposes exactly the same static API. The only
 * change is that the actual logic now lives in the instantiable,
 * dependency-free NexusSupport\Config class, which is what got
 * extracted into the composer package.
 */
final class Config
{
    private static ?ConfigCore $instance = null;

    public static function load(): void
    {
        self::$instance = new ConfigCore(
            require ROOT_PATH . '/config/app.php',
            require ROOT_PATH . '/config/database.php'
        );
    }

    public static function app(string $key): mixed
    {
        return self::instance()->app($key);
    }

    public static function database(string $key): mixed
    {
        return self::instance()->database($key);
    }

    /**
     * Exposes the underlying NexusSupport\Config instance to other
     * facades (currently Database) so they share the exact same
     * loaded config rather than each re-requiring config/*.php
     * independently.
     */
    public static function coreInstance(): ConfigCore
    {
        return self::instance();
    }

    private static function instance(): ConfigCore
    {
        if (self::$instance === null) {
            self::load();
        }

        return self::$instance;
    }
}
