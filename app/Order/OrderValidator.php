<?php

declare(strict_types=1);

final class OrderValidator
{
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

                'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            ]);
    }
}