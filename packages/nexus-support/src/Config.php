<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Instantiable, dependency-free version of Nexus's config reader.
 *
 * Takes plain config arrays via the constructor instead of reaching
 * for ROOT_PATH/require internally. This is what makes it portable
 * outside the Nexus app - the static app::class Config facade
 * (app/Helpers/Config.php) wraps one of these behind the original
 * static API so existing call sites (Config::app(...),
 * Config::database(...)) don't need to change.
 */
final class Config
{
    private array $config;

    public function __construct(array $appConfig, array $databaseConfig)
    {
        $this->config = [
            'app' => $appConfig,
            'database' => $databaseConfig,
        ];
    }

    public function app(string $key): mixed
    {
        return $this->resolveKey($this->config['app'], $key);
    }

    public function database(string $key): mixed
    {
        return $this->resolveKey($this->config['database'], $key);
    }

    private function resolveKey(array $config, string $key, mixed $default = null): mixed
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
