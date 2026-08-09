<?php

declare(strict_types=1);

final class UserValidator
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

    public const ALLOWED_SORT_COLUMNS = [
        'id',
        'username',
        'email',
        'role_id',
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'ASC',
        'DESC',
    ];

    public const DEFAULT_SORT_BY = 'id';

    public const DEFAULT_SORT_DIRECTION = 'DESC';

    public static function validateListing(array $input): Validator
    {
        $validator = (new Validator($input))->validate([
            'page' => 'nullable|integer|min:1',

            'per_page' => 'nullable|integer|in:' .
                implode(',', self::ALLOWED_PAGE_SIZES),

            'search' => 'nullable|max_len:255',

            'role_id' => 'nullable|integer|exists:roles,id',

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

            'role_id' => !empty($data['role_id'])
                ? (int) $data['role_id']
                : null,

            'sort_by' => !empty($data['sort_by'])
                ? $data['sort_by']
                : self::DEFAULT_SORT_BY,

            'sort_direction' => !empty($data['sort_direction'])
                ? strtoupper($data['sort_direction'])
                : self::DEFAULT_SORT_DIRECTION,
        ]);

        return $validator;
    }
}