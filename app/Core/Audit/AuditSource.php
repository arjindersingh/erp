<?php

declare(strict_types=1);

namespace App\Core\Audit;

enum AuditSource: string
{
    case Web = 'web';
    case Api = 'api';
    case Mobile = 'mobile';
    case Console = 'console';
    case Queue = 'queue';
    case Scheduler = 'scheduler';
    case Import = 'import';
    case Integration = 'integration';
    case System = 'system';
}
