<?php
declare(strict_types=1);
class Mailer{
    /**
     * Sends a plain-text email via PHP's built-in mail().
     *
     * No SMTP library exists in this project (no Composer/vendor
     * dependencies at all), so this wraps mail() directly. On
     * XAMPP, mail() requires a local SMTP relay configured in
     * php.ini ([mail function] section) to actually deliver -
     * without that, this will return false or silently no-op
     * depending on your php.ini setup.
     */
    public static function send(
        string $to,
        string $subject,
        string $body
    ): bool {

        $fromAddress = Config::app('mail.from_address');
        $fromName = Config::app('mail.from_name');

        $headers = "From: {$fromName} <{$fromAddress}>\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        return mail($to, $subject, $body, $headers);
    }

    /**
     * Builds an absolute URL back to this app, e.g.
     * "http://nexus.test/activate.php?..." - config's
     * urls.base_url is just "/" (no domain), so the host is
     * read from the current request instead of hardcoded,
     * meaning this works whether you're on nexus.test or
     * localhost without editing config.
     */
    public static function absoluteUrl(string $path): string
    {
        $scheme = (
            !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
        ) ? 'https' : 'http';

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $base = rtrim(Config::app('urls.base_url'), '/');

        return "{$scheme}://{$host}{$base}/" . ltrim($path, '/');
    }
}