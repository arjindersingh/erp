<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum AccessStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Archived = 'archived';

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
