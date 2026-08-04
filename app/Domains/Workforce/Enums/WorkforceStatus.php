<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Enums;

enum WorkforceStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Active = 'active';
    case Suspended = 'suspended';
    case Completed = 'completed';
    case Ended = 'ended';
    case Cancelled = 'cancelled';
    case Archived = 'archived';
}
