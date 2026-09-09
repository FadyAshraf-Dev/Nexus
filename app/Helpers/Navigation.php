<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Navigation as NavigationCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Navigation.
 * Every existing call site is untouched - identical behavior.
 */
class Navigation
{
    public static function currentPage(): string
    {
        return NavigationCore::currentPage();
    }

    public static function activeIf(string $page): string
    {
        return NavigationCore::activeIf($page);
    }
}
