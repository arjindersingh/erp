<?php

declare(strict_types=1);

namespace App\Core\Settings;

final class ValidationResult
{
    private function __construct(
        public bool $valid,
        public bool $fallbackApplied,
        public array $errors,
    ) {
    }

    public static function valid(): self
    {
        return new self(true, false, []);
    }

    public static function invalid(array $errors): self
    {
        return new self(false, false, $errors);
    }

    public static function fallbackApplied(string $field): self
    {
        return new self(false, true, [$field => 'Fallback applied.']);
    }
}
