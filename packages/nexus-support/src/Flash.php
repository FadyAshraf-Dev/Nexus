<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Instantiable, dependency-free version of Nexus's flash-message
 * bag. Takes a SessionStore via the constructor instead of touching
 * $_SESSION directly. The static app::class Flash facade
 * (app/Helpers/Flash.php) wraps one of these (backed by
 * ArraySessionStore) behind the original static API.
 */
final class Flash
{
    private const SESSION_KEY = 'flash';

    public function __construct(private readonly SessionStore $store)
    {
    }

    public function add(string $type, string $message): void
    {
        $messages = $this->store->get(self::SESSION_KEY, []);

        $messages[] = [
            'type' => $type,
            'message' => $message,
        ];

        $this->store->set(self::SESSION_KEY, $messages);
    }

    public function success(string $message): void
    {
        $this->add('success', $message);
    }

    public function error(string $message): void
    {
        $this->add('error', $message);
    }

    public function warning(string $message): void
    {
        $this->add('warning', $message);
    }

    public function info(string $message): void
    {
        $this->add('info', $message);
    }

    public function get(): array
    {
        $messages = $this->store->get(self::SESSION_KEY, []);

        $this->store->remove(self::SESSION_KEY);

        return $messages;
    }

    public function has(): bool
    {
        return !empty($this->store->get(self::SESSION_KEY, []));
    }

    public function display(): string
    {
        $messages = $this->get();

        if (empty($messages)) {
            return '';
        }

        $html = '';

        foreach ($messages as $message) {

            $class = $this->alertClass($message['type']);

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

    private function alertClass(string $type): string
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
