<?php

declare(strict_types=1);

namespace App\Core\Tenancy;

enum TenantStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Inactive = 'inactive';
    case Maintenance = 'maintenance';
    case Terminated = 'terminated';

    public function allowsRequests(): bool
    {
        return $this === self::Active;
    }
}
