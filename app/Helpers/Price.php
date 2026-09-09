<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Price as PriceCore;

/**
 * Static facade over FadyAshrafDev\NexusSupport\Price.
 * Every existing call site is untouched - identical behavior.
 */
final class Price
{
    public static function calculatePrice(
        float $sellingPrice,
        ?string $discountType,
        string|float|int|null $discountValue
    ): float {
        return PriceCore::calculatePrice($sellingPrice, $discountType, $discountValue);
    }

    public static function oldPrice(float $sellingPrice, float $displayPrice): ?float
    {
        return PriceCore::oldPrice($sellingPrice, $displayPrice);
    }
}
