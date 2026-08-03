<?php

declare(strict_types=1);

namespace App\Core\Audit;

enum AuditSeverity: string
{
    case Info = 'info';
    case Notice = 'notice';
    case Warning = 'warning';
    case High = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return str($this->value)->headline()->toString();
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Info => 'gray', self::Notice => 'info', self::Warning => 'warning',
            self::High => 'danger', self::Critical => 'danger',
        };
    }

    public function shouldAlert(): bool
    {
        return in_array($this, [self::High, self::Critical], true);
    }

    public function retentionMultiplier(): int
    {
        return match ($this) {
            self::Info => 1,
            self::Notice, self::Warning => 2,
            self::High, self::Critical => 4,
        };
    }
}
