<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

use PDO;

/**
 * Instantiable, dependency-free version of Nexus's PDO connection
 * factory. Takes a Config instance via the constructor instead of
 * calling the static Config facade internally, and holds the PDO
 * connection as an instance property instead of a static singleton.
 *
 * The static app::class Database facade (app/Core/Database.php)
 * wraps one of these behind the original static API so existing
 * call sites (Database::connection()) don't need to change.
 */
final class Database
{
    private ?PDO $connection = null;

    public function __construct(private readonly Config $config)
    {
    }

    public function connection(): PDO
    {
        if ($this->connection === null) {

            $dsn = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                $this->config->database('driver'),
                $this->config->database('host'),
                $this->config->database('dbname'),
                $this->config->database('charset')
            );

            $this->connection = new PDO(
                $dsn,
                $this->config->database('username'),
                $this->config->database('password'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return $this->connection;
    }
}
