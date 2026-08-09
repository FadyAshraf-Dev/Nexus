<?php

declare(strict_types=1);

final class OrderValidator
{
    public const ALLOWED_PAGE_SIZES = [
        10,
        20,
        50,
    ];

    public const ALLOWED_STATUSES = [
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
    ];

    public static function validateListing(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'page' => 'nullable|integer|min:1',

                'per_page' => 'nullable|integer|in:' .
                    implode(',', self::ALLOWED_PAGE_SIZES),

                'status' => 'nullable|in:' .
                    implode(',', self::ALLOWED_STATUSES),

            ]);
    }

    public static function validateCheckout(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'address' => 'required|min_len:10|max_len:255|regex:/^[\p{L}\p{N}\s,\.\-\/#()]{10,255}$/u',

                'phone' => 'required|regex:/^01[0125]\d{8}$/',
                'coupon_code' => 'nullable|max_len:50'

            ]);
    }

    public static function validateStatusUpdate(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            ]);
    }
}