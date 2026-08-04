<?php

declare(strict_types=1);

namespace App\Core\Settings;

final class SafeInterfaceTokenSet
{
    public function __construct(private array $tokens)
    {
    }

    public function toArray(): array
    {
        return $this->tokens;
    }

    public function toInlineStyle(): string
    {
        return collect($this->tokens)
            ->map(fn ($value, $key) => sprintf('%s: %s;', $key, e($value)))
            ->implode(' ');
    }
}
