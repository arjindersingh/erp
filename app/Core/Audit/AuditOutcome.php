<?php

declare(strict_types=1);

namespace App\Core\Audit;

enum AuditOutcome: string
{
    case Success = 'success';
    case Failed = 'failed';
    case Denied = 'denied';
    case Partial = 'partial';
    case Cancelled = 'cancelled';
    case Reversed = 'reversed';
    case Pending = 'pending';
}
