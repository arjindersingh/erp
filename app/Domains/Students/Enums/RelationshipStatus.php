<?php

declare(strict_types=1);

namespace App\Domains\Students\Enums;

enum RelationshipStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Active = 'active';
    case Suspended = 'suspended';
    case Ended = 'ended';
    case Expired = 'expired';
    case Revoked = 'revoked';
}
