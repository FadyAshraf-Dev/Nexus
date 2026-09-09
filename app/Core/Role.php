<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Role as RoleCore;

/**
 * App-level Role: a non-namespaced alias for the package's
 * FadyAshrafDev\NexusSupport\Role class, so every existing
 * `Role::CUSTOMER` / `Role::VENDOR` / `Role::ADMIN` reference
 * (43 call sites) keeps working unmodified.
 */
final class Role
{
    public const CUSTOMER = RoleCore::CUSTOMER;
    public const VENDOR = RoleCore::VENDOR;
    public const ADMIN = RoleCore::ADMIN;

    private function __construct()
    {
    }
}
