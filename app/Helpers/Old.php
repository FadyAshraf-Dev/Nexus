<?php

declare(strict_types=1);

final class Old
{
    public static function set(array $data): void
    {
        $_SESSION['old'] = $data;
    }

    public static function get(
        string $key,
        mixed $default = ''
    ): mixed {
        return $_SESSION['old'][$key] ?? $default;
    }
    public static function all(): array
    {
        return $_SESSION['old'] ?? [];
    }
    public static function clear(): void
    {
        unset($_SESSION['old']);
    }
}