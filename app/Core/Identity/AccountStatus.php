<?php

declare(strict_types=1);

namespace App\Core\Identity;

enum AccountStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Locked = 'locked';
    case Expired = 'expired';
    case Terminated = 'terminated';

    public function allowsAuthentication(): bool
    {
        return $this === self::Active;
    }
}
