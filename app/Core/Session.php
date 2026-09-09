<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Session as SessionCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Session.
 * Every existing call site is untouched - identical behavior.
 * (Session manages the PHP session lifecycle itself and has zero
 * app-config dependency, so this facade is a pure passthrough.)
 */
class Session
{
    public static function start(): void
    {
        SessionCore::start();
    }

    public static function login(array $user): void
    {
        SessionCore::login($user);
    }

    public static function logout(): void
    {
        SessionCore::logout();
    }

    public static function check(): bool
    {
        return SessionCore::check();
    }

    public static function user(): ?array
    {
        return SessionCore::user();
    }

    public static function id(): ?int
    {
        return SessionCore::id();
    }

    public static function roleId(): ?int
    {
        return SessionCore::roleId();
    }

    public static function refreshRole(int $roleId): void
    {
        SessionCore::refreshRole($roleId);
    }

    public static function guest(): bool
    {
        return SessionCore::guest();
    }

    public static function regenerate(): void
    {
        SessionCore::regenerate();
    }
}
