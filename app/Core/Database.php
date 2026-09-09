<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Database as DatabaseCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Database.
 *
 * Every existing call site in this app (Database::connection())
 * is untouched by the option-2 refactor - this class still exposes
 * exactly the same static API. The only change is that the actual
 * connection logic now lives in the instantiable, dependency-free
 * NexusSupport\Database class, which is what got extracted into
 * the composer package.
 *
 * Deliberately does NOT require config/*.php itself - it reads
 * through Config's own static facade so both facades always agree
 * on the same loaded config, with a single source of truth.
 */
final class Database
{
    private static ?DatabaseCore $instance = null;

    public static function connection(): PDO
    {
        return self::instance()->connection();
    }

    private static function instance(): DatabaseCore
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseCore(Config::coreInstance());
        }

        return self::$instance;
    }
}
