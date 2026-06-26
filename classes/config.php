<?php

declare(strict_types=1);

final class Config
{
    private static array $config = [];

    public static function load(): void
    {
        self::$config = [
            'app' => require __DIR__ . '/../config/app.php',
            'database' => require __DIR__ . '/../config/database.php',
        ];
    }

    public static function app(string $key): mixed
    {
        return self::$config['app'][$key] ?? null;
    }
    public static function database(string $key): mixed
    {
        return self::$config['database'][$key] ?? null;
    }
}