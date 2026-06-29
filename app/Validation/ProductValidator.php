<?php

declare(strict_types=1);

final class ProductValidator
{
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

    private static function validateDiscount(
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
            && (float)$value > 100
        ) {
            $validator->addError(
                'discount_value',
                'Relative discount cannot exceed 100%.'
            );
        }

        if (
            $type === 'fixed'
            && (float)$value > (float)$data['selling_price']
        ) {
            $validator->addError(
                'discount_value',
                'Fixed discount cannot exceed the selling price.'
            );
        }
    }
    
}