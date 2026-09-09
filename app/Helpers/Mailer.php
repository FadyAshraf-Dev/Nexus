<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Mailer as MailerCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Mailer.
 *
 * Every existing call site (Mailer::send(...), Mailer::absoluteUrl(...))
 * is untouched by this refactor. The actual mail() wrapping now
 * lives in the instantiable, dependency-free NexusSupport\Mailer
 * class, constructed here with the same from-address/from-name/
 * base-url (via Config) it always used.
 */
final class Mailer
{
    private static ?MailerCore $instance = null;

    public static function send(
        string $to,
        string $subject,
        string $body
    ): bool {
        return self::instance()->send($to, $subject, $body);
    }

    public static function absoluteUrl(string $path): string
    {
        return self::instance()->absoluteUrl($path);
    }

    private static function instance(): MailerCore
    {
        if (self::$instance === null) {
            self::$instance = new MailerCore(
                Config::app('mail.from_address'),
                Config::app('mail.from_name'),
                Config::app('urls.base_url')
            );
        }

        return self::$instance;
    }
}
