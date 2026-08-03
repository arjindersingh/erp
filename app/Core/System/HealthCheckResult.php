<?php

declare(strict_types=1);

namespace App\Core\System;

final readonly class HealthCheckResult
{
    public function __construct(
        public string $key,
        public string $label,
        public HealthStatus $status,
        public string $summary,
        public ?string $value = null,
    ) {}
}
