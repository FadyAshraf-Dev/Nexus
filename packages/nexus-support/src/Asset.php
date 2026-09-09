<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Instantiable, dependency-free version of Nexus's cache-busted
 * asset URL builder. Takes the admin/base URL prefixes and the
 * public directory root via the constructor instead of calling
 * Config::app(...) or assuming a fixed dirname(__DIR__) layout
 * internally. The static app::class Asset facade
 * (app/Helpers/Asset.php) wraps one of these behind the original
 * static API.
 */
final class Asset
{
    public function __construct(
        private readonly string $adminUrl,
        private readonly string $baseUrl,
        private readonly string $publicPath
    ) {
    }

    public function admin(string $relativePath): string
    {
        $physicalPath = $this->publicPath . '/admin/' . ltrim($relativePath, '/');

        $url = rtrim($this->adminUrl, '/') . '/' . ltrim($relativePath, '/');

        if (file_exists($physicalPath)) {
            return $url . '?v=' . filemtime($physicalPath);
        }

        return $url;
    }

    public function public(string $relativePath): string
    {
        $physicalPath = $this->publicPath . '/' . ltrim($relativePath, '/');

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($relativePath, '/');

        if (file_exists($physicalPath)) {
            return $url . '?v=' . filemtime($physicalPath);
        }

        return $url;
    }
}
