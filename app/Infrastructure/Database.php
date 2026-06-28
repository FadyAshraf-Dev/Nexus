<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {

            $dsn = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                Config::database('driver'),
                Config::database('host'),
                Config::database('dbname'),
                Config::database('charset')
            );

            self::$connection = new PDO(
                $dsn,
                Config::database('username'),
                Config::database('password'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$connection;
    }

    private function __construct() {}
    private function __clone() {}

    public function __wakeup(): void
    {
        throw new LogicException('Cannot unserialize Database.');
    }
}