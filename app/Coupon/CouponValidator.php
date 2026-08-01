<?php

declare(strict_types=1);

final class CouponValidator
{
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