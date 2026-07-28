<?php

declare(strict_types=1);

final class OrderValidator
{
    public static function validateCheckout(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'address' => 'required|min_len:10|max_len:255|regex:/^[\p{L}\p{N}\s,\.\-\/#()]{10,255}$/u',

                'phone' => 'required|regex:/^(010|011|012|015)\d{8}$/',

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