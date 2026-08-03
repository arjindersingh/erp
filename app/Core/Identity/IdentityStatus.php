<?php

declare(strict_types=1);

namespace App\Core\Identity;

enum IdentityStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Archived = 'archived';
    case Revoked = 'revoked';
}
