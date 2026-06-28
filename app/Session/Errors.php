<?php

declare(strict_types=1);

final class Errors
{
    public static function set(array $errors): void
    {
        $_SESSION['errors'] = $errors;
    }

    public static function get(
    string $key,
    mixed $default = ''
): mixed
    {
        return $_SESSION['errors'][$key] ?? $default;
    }

    public static function all(): array
    {
        return $_SESSION['errors'] ?? [];
    }

    public static function has(
        string $key
    ): bool
    {
        return isset($_SESSION['errors'][$key]);
    }

    public static function clear(): void
    {
        unset($_SESSION['errors']);
    }
}