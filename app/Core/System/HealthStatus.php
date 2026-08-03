<?php

declare(strict_types=1);

namespace App\Core\System;

enum HealthStatus: string
{
    case Healthy = 'healthy';
    case Warning = 'warning';
    case Unhealthy = 'unhealthy';

    public function label(): string
    {
        return str($this->value)->headline()->toString();
    }

    public function color(): string
    {
        return match ($this) {
            self::Healthy => 'emerald', self::Warning => 'amber', self::Unhealthy => 'rose',
        };
    }
}
