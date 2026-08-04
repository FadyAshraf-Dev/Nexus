<?php

declare(strict_types=1);

final class NotificationValidator
{
    /**
     * Validates the query params for fetching a user's notifications.
     *
     * Only 'limit' is client-controlled - user_id always comes from
     * the session, never from client input, so there's nothing to
     * validate there (and nothing a client could spoof).
     */
    public static function validateIndex(array $input): Validator
    {
        return (new Validator($input))
            ->validate([

                'limit' => 'nullable|integer|min:1|max:50',

            ]);
    }
}