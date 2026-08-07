<?php

declare(strict_types=1);

final class CouponValidator
{
    public const ALLOWED_PAGE_SIZES = [
        10,
        20,
        50,
    ];

    public const ALLOWED_STATUSES = [
        'active',
        'inactive',
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

    public static function validateApply(array $input): Validator
    {
        $validator = (new Validator($input))->validate([

            'code'
            => 'required|max_len:50',

            'subtotal'
            => 'required|numeric|min:0',

        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $data = $validator->validated();

        $validator->setValidated([

            'code'
            => strtoupper(trim($data['code'])),

            'subtotal'
            => (float) $data['subtotal'],

        ]);

        return $validator;
    }
}