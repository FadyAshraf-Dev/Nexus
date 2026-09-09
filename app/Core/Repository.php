<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Repository as RepositoryCore;

/**
 * App-level Repository: a non-namespaced alias for the package's
 * abstract FadyAshrafDev\NexusSupport\Repository class.
 *
 * Repository is `extends`-ed directly by 12 app repositories
 * (TokenRepository, NotificationRepository, CouponRepository, etc.)
 * so it can't be a delegating facade like Config/Database - PHP has
 * no way to "delegate" a class extension. This keeps the global
 * `Repository` name every one of those 12 files already extends,
 * while the actual logic lives in the portable package class.
 */
abstract class Repository extends RepositoryCore
{
}
