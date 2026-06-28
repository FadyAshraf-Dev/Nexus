<?php
declare(strict_types=1);

final class Asset
{
    public static function admin(string $relativePath): string
    {
        $physicalPath = dirname(__DIR__) . '/public/admin/' . ltrim($relativePath, '/');

        $url = rtrim(Config::app('admin_url'), '/') . '/' . ltrim($relativePath, '/');

        if (file_exists($physicalPath)) {
            return $url . '?v=' . filemtime($physicalPath);
        }

        return $url;
    }

    public static function public(string $relativePath): string
    {
        $physicalPath = dirname(__DIR__) . '/public/' . ltrim($relativePath, '/');

        $url = rtrim(Config::app('base_url'), '/') . '/' . ltrim($relativePath, '/');

        if (file_exists($physicalPath)) {
            return $url . '?v=' . filemtime($physicalPath);
        }

        return $url;
    }
}