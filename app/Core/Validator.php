<?php

declare(strict_types=1);

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $sourceData)
    {
        $this->data = array_map(
            fn($value) => is_string($value) ? trim($value) : $value,
            $sourceData
        );
    }

    /**
     * Validate the supplied data against the provided rules.
     */
    public function validate(array $rules): self
    {
        foreach ($rules as $field => $ruleString) {

            $value = $this->data[$field] ?? null;

            foreach ($this->splitRuleString($ruleString) as $rule) {

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
    public function setValidated(array $data): self
    {
        $this->data = $data;

        return $this;
    }
    /**
     * -----------------------------
     * Validation Rules
     * -----------------------------
     */
    private function validateBoolean(
        string $field,
        mixed $value
    ): void {

        if (
            $value === null ||
            $value === ''
        ) {
            return;
        }

        $allowed = [

            true,
            false,
            1,
            0,
            "1",
            "0",
            "true",
            "false"

        ];

        if (!in_array($value, $allowed, true)) {

            $this->errors[$field] =
                $this->label($field) . ' must be true or false.';

        }

    }

    private function validateRegex(
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

        if (
            $parameter === null ||
            @preg_match((string) $parameter, '') === false
        ) {
            throw new InvalidArgumentException(
                "Invalid regex supplied for '{$field}'."
            );
        }

        if (!preg_match((string) $parameter, (string) $value)) {

            $this->errors[$field] =
                $this->label($field) . ' has an invalid format.';

        }

    }

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

    /**
     * -----------------------------
     * Helpers
     * -----------------------------
     */

    /**
     * Splits a pipe-delimited rule string into individual rule tokens.
     *
     * A plain explode('|', $ruleString) breaks whenever a regex: rule's
     * own pattern contains a | (e.g. alternation like (010|011)), since
     * that | gets mistaken for a rule separator. This walks the string
     * once and treats a regex:/pattern/flags segment as opaque, only
     * splitting on | outside of it.
     *
     * Assumes PCRE delimiters where the opening and closing delimiter
     * are the same character (the common /pattern/ form used throughout
     * this app). Bracket-style delimiters like (...), {...}, [...] are
     * not handled, since no rule in this app currently uses them.
     */
    private function splitRuleString(string $ruleString): array
    {
        $tokens = [];
        $length = strlen($ruleString);
        $start = 0;
        $i = 0;

        while ($i < $length) {

            if (
                substr($ruleString, $i, 6) === 'regex:'
                && $i + 6 < $length
            ) {

                $delimiter = $ruleString[$i + 6];
                $i += 7; // past "regex:" + opening delimiter

                while ($i < $length && $ruleString[$i] !== $delimiter) {
                    $i++;
                }

                if ($i < $length) {
                    $i++; // consume closing delimiter
                }

                // Consume trailing regex flags (e.g. u, i, m).
                while ($i < $length && ctype_alpha($ruleString[$i])) {
                    $i++;
                }

                continue;

            }

            if ($ruleString[$i] === '|') {
                $tokens[] = substr($ruleString, $start, $i - $start);
                $i++;
                $start = $i;
                continue;
            }

            $i++;

        }

        $tokens[] = substr($ruleString, $start);

        return $tokens;
    }

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