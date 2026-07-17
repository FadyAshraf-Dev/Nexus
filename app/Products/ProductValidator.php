<?php

declare(strict_types=1);

final class ProductValidator
{
    public const DEFAULT_PER_PAGE = 10;

    public const ALLOWED_PAGE_SIZES = [
        1,
        5,
        10,
        15,
        20,
        25,
    ];

    public const ALLOWED_STATUSES = [
        'active',
        'inactive',
    ];

    public const ALLOWED_SORT_COLUMNS = [
        'created_at',
        'product_name',
        'selling_price',
        'stock_quantity',
        'status',
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'ASC',
        'DESC',
    ];

    public const DEFAULT_SORT_BY = 'created_at';

    public const DEFAULT_SORT_DIRECTION = 'DESC';

    public static function validate(array $data): Validator
    {
        $validator = (new Validator($data))->validate([

            'product_name'
            => 'required|min_len:3|max_len:150',

            'short_description'
            => 'required|min_len:10|max_len:170',

            'full_description'
            => 'required|min_len:20',

            'category_id'
            => 'required|integer|exists:categories,id',

            'status'
            => 'required|in:active,inactive',

            'brand'
            => 'nullable|max_len:100',

            'cost_price'
            => 'required|numeric|min:0',

            'selling_price'
            => 'required|numeric|min:cost_price',

            'discount_type'
            => 'nullable|in:relative,fixed',

            'discount_value'
            => 'required_if:discount_type|numeric|min:0',

            'stock_quantity'
            => 'required|integer|min:0',

            'low_stock_threshold'
            => 'nullable|integer|min:0',

        ]);

        if ($validator->passes()) {

            self::validateDiscount($validator, $validator->validated());

        }

        return $validator;
    }
public static function validateListing(array $input): Validator
{
    $validator = (new Validator($input))->validate([
        'page' => 'nullable|integer|min:1',

        'per_page' => 'nullable|integer|in:' .
            implode(',', self::ALLOWED_PAGE_SIZES),

        'search' => 'nullable|max_len:255',

        'category_id' => 'nullable|integer|exists:categories,id',

        'status' => 'nullable|in:' .
            implode(',', self::ALLOWED_STATUSES),

        'sort_by' => 'nullable|in:' .
            implode(',', self::ALLOWED_SORT_COLUMNS),

        'sort_direction' => 'nullable|in:' .
            implode(',', self::ALLOWED_SORT_DIRECTIONS),
    ]);

    if ($validator->fails()) {
        return $validator;
    }

    $data = $validator->validated();

    $validator->setValidated([
        'page' => !empty($data['page'])
            ? (int) $data['page']
            : 1,

        'per_page' => !empty($data['per_page'])
            ? (int) $data['per_page']
            : self::DEFAULT_PER_PAGE,

        'search' => !empty($data['search'])
            ? $data['search']
            : null,

        'category_id' => !empty($data['category_id'])
            ? (int) $data['category_id']
            : null,

        'status' => !empty($data['status'])
            ? $data['status']
            : null,

        'sort_by' => !empty($data['sort_by'])
            ? $data['sort_by']
            : self::DEFAULT_SORT_BY,

        'sort_direction' => !empty($data['sort_direction'])
            ? strtoupper($data['sort_direction'])
            : self::DEFAULT_SORT_DIRECTION,
    ]);

    return $validator;
}    private static function validateDiscount(
        Validator $validator,
        array $data
    ): void {

        $type = $data['discount_type'] ?? null;
        $value = $data['discount_value'] ?? null;

        if ($type === null || $value === null || $value === '') {
            return;
        }

        if (
            $type === 'relative'
            && (float) $value > 100
        ) {
            $validator->addError(
                'discount_value',
                'Relative discount cannot exceed 100%.'
            );
        }

        if (
            $type === 'fixed'
            && (float) $value > (float) $data['selling_price']
        ) {
            $validator->addError(
                'discount_value',
                'Fixed discount cannot exceed the selling price.'
            );
        }
    }
    public static function validateImageCount(array $files): ?string
    {
        $count = count(array_filter(
            $files['name'],
            fn($name) => $name !== ''
        ));

        if ($count < 1) {
            return 'Please upload at least one image.';
        }

        if ($count > 5) {
            return 'You may upload a maximum of 5 images.';
        }

        return null;
    }

}