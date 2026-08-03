<?php

declare(strict_types=1);

namespace App\Core\System;

use Illuminate\Support\Collection;

final readonly class SystemHealthReport
{
    /** @param Collection<int, HealthCheckResult> $checks */
    public function __construct(
        public HealthStatus $status,
        public Collection $checks,
        public string $checkedAt,
    ) {}
}
