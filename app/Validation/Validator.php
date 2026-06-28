<?php

declare(strict_types=1);

class Validator
{
    private ?PDO $pdo = null;

    private function pdo(): PDO
    {
        return $this->pdo ??= Database::connection();
    }
    private array $data;
    private array $errors = [];

    public function __construct(array $sourceData)
    {
        $this->data = array_map(
            fn($value) => is_string($value) ? trim($value) : $value,
            $sourceData
        );
        $this->pdo = Database::connection();

    }

    /**
     * Validate the supplied data against the provided rules.
     */
    public function validate(array $rules): self
    {
        foreach ($rules as $field => $ruleString) {

            $value = $this->data[$field] ?? null;

            foreach (explode('|', $ruleString) as $rule) {

                $parameter = null;
                if (
                    $rule === 'nullable'
                    && ($value === null || $value === '')
                ) {
                    break;
                }

                if (str_contains($rule, ':')) {
                    [$rule, $parameter] = explode(':', $rule, 2);
                }
                $method = $this->resolveRuleMethod($rule);

                if (!method_exists($this, $method)) {
                    continue;
                }

                $this->$method(
                    $field,
                    $value,
                    $this->resolveRuleArgument($parameter)
                );

                if (isset($this->errors[$field])) {
                    break;
                }
            }
        }

        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->data;
    }

    /**
     * -----------------------------
     * Validation Rules
     * -----------------------------
     */

    private function validateRequired(string $field, mixed $value): void
    {
        if ($value === null || $value === '') {
            $this->errors[$field] = $this->label($field) . ' is required.';
        }
    }

    private function validateNumeric(string $field, mixed $value): void
    {
        if (
            $value !== null &&
            $value !== '' &&
            !is_numeric($value)
        ) {
            $this->errors[$field] = $this->label($field) . ' must be a valid number.';
        }
    }

    private function validateInteger(string $field, mixed $value): void
    {

        if (
            $value !== null &&
            $value !== '' &&
            (!is_numeric($value) || (int) $value != $value)
        ) {
            $this->errors[$field] = $this->label($field) . ' must be a whole integer.';
        }
    }

    private function validateMin(string $field, mixed $value, mixed $parameter): void
    {
        if (
            $value !== null &&
            $value !== '' &&
            is_numeric($value) &&
            (float) $value < (float) $parameter
        ) {
            $this->errors[$field] = $this->label($field) . " cannot be less than {$parameter}.";
        }
    }

    private function validateMax(string $field, mixed $value, mixed $parameter): void
    {
        if (
            $value !== null &&
            $value !== '' &&
            is_numeric($value) &&
            (float) $value > (float) $parameter
        ) {
            $this->errors[$field] = $this->label($field) . " cannot exceed {$parameter}.";
        }
    }

    private function validateMinLen(string $field, mixed $value, mixed $parameter): void
    {
        if (
            $value !== null &&
            $value !== '' &&
            mb_strlen((string) $value) < (int) $parameter
        ) {
            $this->errors[$field] = $this->label($field) . " must be at least {$parameter} characters.";
        }
    }

    private function validateMaxLen(string $field, mixed $value, mixed $parameter): void
    {
        if (
            $value !== null &&
            $value !== '' &&
            mb_strlen((string) $value) > (int) $parameter
        ) {
            $this->errors[$field] = $this->label($field) . " cannot exceed {$parameter} characters.";
        }
    }

    private function validateIn(string $field, mixed $value, mixed $parameter): void
    {
        $allowed = explode(',', (string) $parameter);

        if (
            $value !== null &&
            $value !== '' &&
            !in_array($value, $allowed, true)
        ) {
            $this->errors[$field] = "Invalid selection for {$this->label($field)}.";
        }
    }

    private function validateEmail(string $field, mixed $value): void
    {
        if (
            $value !== null &&
            $value !== '' &&
            !filter_var($value, FILTER_VALIDATE_EMAIL)
        ) {
            $this->errors[$field] =
                $this->label($field) . ' must be a valid email address.';
        }
    }

    private function validateRequiredIf(
        string $field,
        mixed $value,
        mixed $parameter
    ): void {
        if ($parameter === null || $parameter === '') {
            return;
        }

        $this->validateRequired($field, $value);
    }

    private function validateExists(
        string $field,
        mixed $value,
        mixed $parameter
    ): void {

        if (
            $value === null ||
            $value === ''
        ) {
            return;
        }

        [$table, $column] = explode(',', (string) $parameter, 2);
        $sql = sprintf(
            "SELECT 1
         FROM `%s`
         WHERE `%s` = :value
         LIMIT 1",
            $table,
            $column
        );

        $statement = $this->pdo()->prepare($sql);
        $statement->execute([
            'value' => $value
        ]);

        if (!$statement->fetchColumn()) {
            $this->errors[$field] =
                $this->label($field) . ' does not exist.';
        }
    }

    /**
     * -----------------------------
     * Helpers
     * -----------------------------
     */

    private function resolveRuleArgument(?string $parameter): mixed
    {
        if ($parameter === null) {
            return null;
        }

        return $this->data[$parameter] ?? $parameter;
    }
    private function resolveRuleMethod(string $rule): string
    {
        return 'validate' . str_replace(
            ' ',
            '',
            ucwords(str_replace('_', ' ', $rule))
        );
    }

    private function label(string $field): string
    {
        return ucfirst(
            str_replace('_', ' ', $field)
        );
    }

    public function addError(
        string $field,
        string $message
    ): void {

        $this->errors[$field] = $message;

    }

}