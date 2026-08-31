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

    public const MIN_PASSWORD_LENGTH = 8;

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

    /**
     * Validates a new-account signup submission.
     *
     * username is restricted to letters/numbers/underscore -
     * matches the varchar(50) column and avoids characters that
     * would be awkward in a URL or display context later.
     */
    public static function validateRegistration(array $input): Validator
    {
        $validator = (new Validator($input))->validate([
            'email' => 'required|email|max_len:100',

            'username' => 'required|min_len:3|max_len:50|regex:/^[a-zA-Z0-9_]+$/',

            'password' => 'required|min_len:' . self::MIN_PASSWORD_LENGTH,

            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $validator;
        }

        $data = $validator->validated();

        $validator->setValidated([
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
        ]);

        return $validator;
    }

    /**
     * Validates the "edit info" submission on the profile page.
     * Shape only - email-already-taken and username-already-taken
     * checks need a DB lookup excluding the current user's own
     * row, which the generic validator has no rule for, so
     * UserService::updateProfile() does those explicitly.
     */
    public static function validateProfileUpdate(array $input): Validator
    {
        return (new Validator($input))->validate([
            'email' => 'required|email|max_len:100',

            'username' => 'required|min_len:3|max_len:50|regex:/^[a-zA-Z0-9_]+$/',
        ]);
    }

    /**
     * Validates a change-password submission from a logged-in
     * user (profile page). current_password is checked against
     * the hash by UserService - this only validates shape.
     */
    public static function validateChangePassword(array $input): Validator
    {
        return (new Validator($input))->validate([
            'current_password' => 'required',

            'new_password' => 'required|min_len:' . self::MIN_PASSWORD_LENGTH,

            'new_password_confirmation' => 'required|same:new_password',
        ]);
    }

    public static function validateForgotPassword(array $input): Validator
    {
        return (new Validator($input))->validate([
            'email' => 'required|email|max_len:100',
        ]);
    }

    /**
     * Validates the "set new password" submission on
     * reset-password.php. The token itself is verified separately
     * by PasswordReset::consume() - this only validates the new
     * password's shape.
     */
    public static function validateResetPassword(array $input): Validator
    {
        return (new Validator($input))->validate([
            'token' => 'required',

            'password' => 'required|min_len:' . self::MIN_PASSWORD_LENGTH,

            'password_confirmation' => 'required|same:password',
        ]);
    }
}