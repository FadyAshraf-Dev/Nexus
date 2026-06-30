<?php
declare(strict_types=1);

class Flash
{
    private const SESSION_KEY = 'flash';

    public static function add(
        string $type,
        string $message
    ): void {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        $_SESSION[self::SESSION_KEY][] = [

            'type' => $type,
            'message' => $message

        ];


    }
    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('error', $message);
    }

    public static function warning(string $message): void
    {
        self::add('warning', $message);
    }

    public static function info(string $message): void
    {
        self::add('info', $message);
    }
    public static function get(): array
    {
        $messages = $_SESSION[self::SESSION_KEY] ?? [];

        unset($_SESSION[self::SESSION_KEY]);

        return $messages;
    }
    public static function has(): bool
    {
        return !empty($_SESSION[self::SESSION_KEY]);
    }
    public static function display(): string
    {
        $messages = self::get();

        if (empty($messages)) {
            return '';
        }

        $html = '';

        foreach ($messages as $message) {

            $class = self::alertClass($message['type']);

            $html .= '
        <div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($message['message']) . '
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>';
        }

        return $html;
    }
    private static function alertClass(string $type): string
    {
        return match ($type) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-secondary',
        };
    }
}