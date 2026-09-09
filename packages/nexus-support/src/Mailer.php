<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Instantiable, dependency-free version of Nexus's mail() wrapper.
 * Takes from-address/from-name/base-url via the constructor instead
 * of calling Config::app(...) internally. The static app::class
 * Mailer facade (app/Helpers/Mailer.php) wraps one of these behind
 * the original static API.
 *
 * No SMTP library is used - this wraps PHP's built-in mail()
 * directly, same as the original. On XAMPP, mail() requires a
 * local SMTP relay configured in php.ini ([mail function] section)
 * to actually deliver - without that, send() will return false or
 * silently no-op depending on your php.ini setup.
 */
final class Mailer
{
    public function __construct(
        private readonly string $fromAddress,
        private readonly string $fromName,
        private readonly string $baseUrl
    ) {
    }

    public function send(
        string $to,
        string $subject,
        string $body
    ): bool {

        $headers = "From: {$this->fromName} <{$this->fromAddress}>\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        return mail($to, $subject, $body, $headers);
    }

    /**
     * Builds an absolute URL back to this app, e.g.
     * "http://nexus.test/activate.php?..." - the configured
     * base URL is just "/" (no domain), so the host is read from
     * the current request instead of hardcoded, meaning this works
     * whether you're on nexus.test or localhost without editing
     * config.
     */
    public function absoluteUrl(string $path): string
    {
        $scheme = (
            !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
        ) ? 'https' : 'http';

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $base = rtrim($this->baseUrl, '/');

        return "{$scheme}://{$host}{$base}/" . ltrim($path, '/');
    }
}
