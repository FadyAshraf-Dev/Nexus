<?php

declare(strict_types=1);

final class ReviewValidator
{
    public const DEFAULT_PER_PAGE = 10;

    public const ALLOWED_PAGE_SIZES = [
        10,
        20,
        50,
    ];

    public const ALLOWED_STATUSES = [
        'active',
        'hidden',
    ];

    public const ALLOWED_SORT_COLUMNS = [
        'created_at',
        'rate',
        'updated_at',
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'ASC',
        'DESC',
    ];

    public static function validateListing(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'page' => 'nullable|integer|min:1',

                'per_page' => 'nullable|integer|in:' .
                    implode(',', self::ALLOWED_PAGE_SIZES),

                'search' => 'nullable|max_len:255',

                'product_id' => 'nullable|integer|exists:products,id',

                'min_rate' => 'nullable|integer|min:1|max:10',

                'status' => 'nullable|in:' .
                    implode(',', self::ALLOWED_STATUSES),

                'sort_by' => 'nullable|in:' .
                    implode(',', self::ALLOWED_SORT_COLUMNS),

                'sort_direction' => 'nullable|in:' .
                    implode(',', self::ALLOWED_SORT_DIRECTIONS),

            ]);
    }

    public static function validateSubmit(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'product_id' => 'required|integer',
                'rate'       => 'required|integer|min:1|max:10',
                'content'    => 'nullable|max_len:1000',

            ]);
    }

    public static function validateUpdate(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'rate'    => 'required|integer|min:1|max:10',
                'content' => 'nullable|max_len:1000',

            ]);
    }

    public static function validateProductId(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'product_id' => 'required|integer',

            ]);
    }
}