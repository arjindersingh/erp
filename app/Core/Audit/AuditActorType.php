<?php

declare(strict_types=1);

namespace App\Core\Audit;

enum AuditActorType: string
{
    case User = 'user';
    case SiteAdministrator = 'site_administrator';
    case ServiceAccount = 'service_account';
    case ApiClient = 'api_client';
    case ScheduledJob = 'scheduled_job';
    case QueueWorker = 'queue_worker';
    case System = 'system';
    case Integration = 'integration';
    case Unknown = 'unknown';
}
