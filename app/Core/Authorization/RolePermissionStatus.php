<?php

declare(strict_types=1);

namespace App\Core\Authorization;

enum RolePermissionStatus: string
{
    case Active = 'active';
    case Revoked = 'revoked';
    case Suspended = 'suspended';
}
