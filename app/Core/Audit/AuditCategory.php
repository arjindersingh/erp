<?php

declare(strict_types=1);

namespace App\Core\Audit;

enum AuditCategory: string
{
    case Authentication = 'authentication';
    case Authorization = 'authorization';
    case Security = 'security';
    case DataChange = 'data_change';
    case Workflow = 'workflow';
    case Financial = 'financial';
    case Academic = 'academic';
    case HumanResource = 'human_resource';
    case Configuration = 'configuration';
    case Import = 'import';
    case Export = 'export';
    case Report = 'report';
    case Document = 'document';
    case Communication = 'communication';
    case Integration = 'integration';
    case System = 'system';
    case Privacy = 'privacy';
    case Impersonation = 'impersonation';

    public function label(): string
    {
        return str($this->value)->headline()->toString();
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Security, self::Impersonation => 'danger',
            self::Authorization, self::Financial, self::Privacy => 'warning',
            self::Academic, self::HumanResource, self::Workflow => 'info',
            default => 'gray',
        };
    }

    public function defaultSeverity(): AuditSeverity
    {
        return match ($this) {
            self::Security, self::Impersonation => AuditSeverity::High,
            self::Authorization, self::Financial, self::Privacy => AuditSeverity::Notice,
            default => AuditSeverity::Info,
        };
    }

    public function defaultRetentionDays(): ?int
    {
        return match ($this) {
            self::Financial => 2920,
            self::Authentication, self::Authorization, self::Security => 730,
            default => 730,
        };
    }

    public function requiresMasking(): bool
    {
        return in_array($this, [self::Authentication, self::Financial, self::HumanResource, self::Privacy, self::Impersonation], true);
    }
}
