<?php

declare(strict_types=1);

final class Price
{
    private function __construct()
    {
    }

    public static function calculatePrice(
        float $sellingPrice,
        ?string $discountType,
        string|float|int|null $discountValue
    ): float {
        $discountValue = $discountValue !== null
            ? (float) $discountValue
            : null;
        $displayPrice = $sellingPrice;

        if ($discountType === 'relative') {

            $displayPrice -=
                $sellingPrice *
                ((float) $discountValue / 100);

        } elseif ($discountType === 'fixed') {

            $displayPrice -=
                (float) $discountValue;

        }

        return round(
            max(0, $displayPrice),
            2
        );
    }

    public static function oldPrice(
        float $sellingPrice,
        float $displayPrice
    ): ?float {

        return $displayPrice < $sellingPrice
            ? round($sellingPrice, 2)
            : null;
    }
}