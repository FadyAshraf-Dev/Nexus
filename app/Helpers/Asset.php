<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Asset as AssetCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Asset.
 *
 * Every existing call site (Asset::admin(...), Asset::public(...))
 * is untouched by this refactor. The actual cache-busted URL
 * building now lives in the instantiable, dependency-free
 * NexusSupport\Asset class, constructed here with the same
 * admin/base URLs (via Config) and public path
 * (dirname(__DIR__) . '/public', exactly as the original resolved
 * it) it always used.
 */
final class Asset
{
    private static ?AssetCore $instance = null;

    public static function admin(string $relativePath): string
    {
        return self::instance()->admin($relativePath);
    }

    public static function public(string $relativePath): string
    {
        return self::instance()->public($relativePath);
    }

    private static function instance(): AssetCore
    {
        if (self::$instance === null) {
            self::$instance = new AssetCore(
                Config::app('urls.admin_url'),
                Config::app('urls.base_url'),
                dirname(__DIR__) . '/public'
            );
        }

        return self::$instance;
    }
}
