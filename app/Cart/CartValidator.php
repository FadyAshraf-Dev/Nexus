<?php

declare(strict_types=1);

final class CartValidator
{
    public const DEFAULT_QUANTITY = 1;

    /**
     * Validates Add-To-Cart requests.
     */
    public static function validateAdd(array $input): Validator
    {
        $validator = (new Validator($input))->validate([

            'product_id'
                => 'required|integer|exists:products,id',

            'quantity'
                => 'nullable|integer|min:1',

        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $data = $validator->validated();

        $validator->setValidated([

            'product_id' => (int) $data['product_id'],

            'quantity' => !empty($data['quantity'])
                ? (int) $data['quantity']
                : self::DEFAULT_QUANTITY,

        ]);

        return $validator;
    }

    /**
     * Validates Update Quantity requests.
     */
    public static function validateUpdate(array $input): Validator
    {
        $validator = (new Validator($input))->validate([

            'product_id'
                => 'required|integer|exists:products,id',

            'quantity'
                => 'required|integer|min:1',

        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $data = $validator->validated();

        $validator->setValidated([

            'product_id' => (int) $data['product_id'],

            'quantity' => (int) $data['quantity'],

        ]);

        return $validator;
    }

    /**
     * Validates Remove Item requests.
     */
    public static function validateRemove(array $input): Validator
    {
        $validator = (new Validator($input))->validate([

            'product_id'
                => 'required|integer|exists:products,id',

        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $data = $validator->validated();

        $validator->setValidated([

            'product_id' => (int) $data['product_id'],

        ]);

        return $validator;
    }
}