<?php

declare(strict_types=1);

namespace App\Core\Identity;

enum MembershipType: string
{
    case SiteAdministration = 'site_administration';
    case TenantAdministration = 'tenant_administration';
    case Management = 'management';
    case Employee = 'employee';
    case Teacher = 'teacher';
    case Student = 'student';
    case Guardian = 'guardian';
    case Alumni = 'alumni';
    case Service = 'service';
    case External = 'external';

    public function label(): string
    {
        return match ($this) {
            self::SiteAdministration => 'Site Administration',
            self::TenantAdministration => 'Tenant Administration',
            self::Management => 'Management',
            self::Employee => 'Employee',
            self::Teacher => 'Teacher',
            self::Student => 'Student',
            self::Guardian => 'Guardian',
            self::Alumni => 'Alumni',
            self::Service => 'Service',
            self::External => 'External',
        };
    }

    public function requiresPerson(): bool
    {
        return ! in_array($this, [
            self::SiteAdministration,
            self::Service,
            self::External,
        ], true);
    }
}
