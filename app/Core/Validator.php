<?php

declare(strict_types=1);

use FadyAshrafDev\NexusSupport\Validator as ValidatorCore;

/**
 * App-level Validator: extends the package's instantiable
 * ValidatorCore, defaulting the $pdo constructor argument to
 * Database::connection() when the caller doesn't supply one.
 *
 * This preserves every existing `new Validator($input)` call site
 * (24 of them across app/*Validator.php) with zero edits, while the
 * actual validation logic now lives in the portable, PDO-injected
 * FadyAshrafDev\NexusSupport\Validator that got extracted into the
 * composer package.
 */
class Validator extends ValidatorCore
{
    public function __construct(array $sourceData, ?PDO $pdo = null)
    {
        parent::__construct($sourceData, $pdo ?? Database::connection());
    }
}
