<?php

declare(strict_types=1);

final class Config
{
    private static array $config = [];

    public static function load(): void
    {
        self::$config = [
            'app' => require ROOT_PATH . '/config/app.php',
            'database' => require ROOT_PATH . '/config/database.php',
        ];
    }

    public static function app(string $key): mixed
    {
        return self::resolveKey(self::$config['app'], $key);
    }
    public static function database(string $key): mixed
    {
        return self::resolveKey(self::$config['database'], $key);
    }
    private static function resolveKey(array $config, string $key,mixed $default = null
): mixed
    {
        $keys = explode('.', $key);

        foreach ($keys as $segment) {

            if (
                !is_array($config)
                || !array_key_exists($segment, $config)
            ) {
                return $default;
            }

            $config = $config[$segment];
        }

        return $config;
    }
}