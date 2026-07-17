<?php

declare(strict_types=1);

final class Cookie
{
    public static function set(
        string $name,
        string $value,
        int $expires
    ): void {

        setcookie(
            $name,
            $value,
            [
                'expires'  => $expires,
                'path'      => '/',
                'secure'    => isset($_SERVER['HTTPS']),
                'httponly'  => true,
                'samesite'  => 'Lax',
            ]
        );
    }

    public static function get(
        string $name
    ): ?string {

        return $_COOKIE[$name] ?? null;

    }

    public static function exists(
        string $name
    ): bool {

        return isset($_COOKIE[$name]);

    }

    public static function delete(
        string $name
    ): void {

        self::set(
            $name,
            '',
            time() - 3600
        );

    }
}