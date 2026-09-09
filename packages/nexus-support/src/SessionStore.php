<?php

declare(strict_types=1);

namespace FadyAshrafDev\NexusSupport;

/**
 * Minimal storage contract used by Errors, Old, and Flash instead
 * of touching $_SESSION directly. Lets those classes be constructed
 * against any array-like session backend (the default
 * ArraySessionStore wraps $_SESSION itself, so app behavior is
 * unchanged), rather than assuming PHP's superglobal session is
 * always present the way it is in a real Nexus request.
 */
interface SessionStore
{
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;
    public function remove(string $key): void;
}
