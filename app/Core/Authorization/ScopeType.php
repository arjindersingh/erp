<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum ScopeType: string
{
    case Tenant = 'tenant';
    case Company = 'company';
    case Campus = 'campus';
    case Institute = 'institute';

    public function label(): string
    {
        return match ($this) {
            self::Tenant => 'Tenant',
            self::Company => 'Company',
            self::Campus => 'Campus',
            self::Institute => 'Institute',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Tenant => 'primary',
            self::Company => 'info',
            self::Campus => 'warning',
            self::Institute => 'success',
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::Tenant => 0,
            self::Company => 1,
            self::Campus => 2,
            self::Institute => 3,
        };
    }

    public function parentType(): ?self
    {
        return match ($this) {
            self::Tenant => null,
            self::Company => self::Tenant,
            self::Campus => self::Company,
            self::Institute => self::Campus,
        };
    }
}
