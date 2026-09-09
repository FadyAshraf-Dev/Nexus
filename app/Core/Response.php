<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Response as ResponseCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Response.
 *
 * Every existing call site (Response::redirectAdmin(), redirectBase(),
 * forbidden(), json()) is untouched by this refactor - this class
 * still exposes exactly the same static API. The actual logic now
 * lives in the instantiable, dependency-free NexusSupport\Response
 * class, constructed here with the same admin/base URLs (read via
 * the existing Config facade) and the same 403 template path
 * (__DIR__-relative, exactly as the original resolved it) it always
 * used.
 */
final class Response
{
    private static ?ResponseCore $instance = null;

    public static function redirectAdmin($url)
    {
        self::instance()->redirectAdmin($url);
    }

    public static function redirectBase($url)
    {
        self::instance()->redirectBase($url);
    }

    public static function forbidden()
    {
        self::instance()->forbidden();
    }

    public static function json(mixed $data, int $status = 200): never
    {
        self::instance()->json($data, $status);
    }

    private static function instance(): ResponseCore
    {
        if (self::$instance === null) {
            self::$instance = new ResponseCore(
                Config::app('urls.admin_url'),
                Config::app('urls.base_url'),
                __DIR__ . '/../admin/error-403.html'
            );
        }

        return self::$instance;
    }
}
