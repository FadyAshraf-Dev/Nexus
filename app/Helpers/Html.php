<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Html as HtmlCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Html.
 * Every existing call site is untouched - identical behavior.
 */
class Html
{
    public static function escape(?string $data): string
    {
        return HtmlCore::escape($data);
    }

    public static function slug(string $text): string
    {
        return HtmlCore::slug($text);
    }
}
