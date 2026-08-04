<?php

declare(strict_types=1);

namespace App\Core\Attribution;

final class AuditValueSanitizer
{
    /** @param array<string, mixed> $values */
    public function sanitize(array $values): array
    {
        $sanitized = [];
        foreach ($values as $key => $value) {
            if ($this->isSensitiveField($key)) {
                $sanitized[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value);
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function isSensitiveField(string $key): bool
    {
        $field = strtolower($key);

        return str_contains($field, 'password')
            || str_contains($field, 'secret')
            || str_contains($field, 'token')
            || str_contains($field, 'otp')
            || str_contains($field, 'private_key')
            || str_contains($field, 'card')
            || str_contains($field, 'bank_account');
    }
}
