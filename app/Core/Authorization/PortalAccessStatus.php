<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum PortalAccessStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Revoked = 'revoked';
    case Expired = 'expired';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
