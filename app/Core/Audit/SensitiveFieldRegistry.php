<?php

declare(strict_types=1);

namespace App\Core\Audit;

final class SensitiveFieldRegistry
{
    /** @var list<string> */
    private array $excluded;

    /** @var list<string> */
    private array $sensitive;

    public function __construct()
    {
        $this->excluded = config('audit.excluded_fields', []);
        $this->sensitive = config('audit.sensitive_fields', []);
    }

    public function isSensitive(string $modelClass, string $field): bool
    {
        return $this->matches($field, $this->sensitive);
    }

    public function exclude(string $field): bool
    {
        return $this->matches($field, $this->excluded)
            || preg_match('/(?:password|token|secret|recovery_code|private_key)/i', $field) === 1;
    }

    public function hashOnly(string $field): bool
    {
        return preg_match('/(?:government_id|aadhaar|pan_number)/i', $field) === 1;
    }

    public function mask(string $field, mixed $value): mixed
    {
        if ($value === null || ! is_scalar($value)) {
            return $value === null ? null : '[MASKED]';
        }

        $text = (string) $value;
        if (str_contains(strtolower($field), 'email') && str_contains($text, '@')) {
            [$local, $domain] = explode('@', $text, 2);

            return mb_substr($local, 0, 1).'***@'.$domain;
        }

        $visible = min(4, max(0, mb_strlen($text) - 2));

        return $visible === 0
            ? str_repeat('*', max(4, mb_strlen($text)))
            : str_repeat('*', max(4, mb_strlen($text) - $visible)).mb_substr($text, -$visible);
    }

    /** @param list<string> $patterns */
    private function matches(string $field, array $patterns): bool
    {
        $field = strtolower($field);

        return collect($patterns)->contains(fn (string $pattern): bool => str_contains($field, strtolower($pattern)));
    }
}
