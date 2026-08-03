<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum RoleAssignmentStatus: string
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
            self::Active => 'success',
            self::Pending => 'warning',
            self::Inactive, self::Expired => 'gray',
            self::Suspended, self::Terminated => 'danger',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
