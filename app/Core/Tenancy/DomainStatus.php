<?php

declare(strict_types=1);

namespace App\Core\Tenancy;

enum DomainStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Revoked = 'revoked';
}
