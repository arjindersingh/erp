<?php

declare(strict_types=1);

namespace App\Core\Identity;

enum MembershipStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Expired = 'expired';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
            self::Expired => 'Expired',
            self::Terminated => 'Terminated',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'success',
            self::Inactive => 'gray',
            self::Suspended => 'danger',
            self::Expired => 'gray',
            self::Terminated => 'danger',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isSelectable(): bool
    {
        return $this === self::Active;
    }
}
