<?php

declare(strict_types=1);

final class VendorRequestValidator
{
    public static function validateCreate(array $input): Validator
    {
        return (new Validator($input))->validate([
            'store_name' => 'required|min_len:2|max_len:100',

            'description' => 'required|min_len:20|max_len:2000',

            'phone' => 'required|min_len:6|max_len:30|regex:/^[0-9+\-\s()]+$/',
        ]);
    }

    public static function validateReject(array $input): Validator
    {
        return (new Validator($input))->validate([
            'reason' => 'required|min_len:5|max_len:255',
        ]);
    }
}
