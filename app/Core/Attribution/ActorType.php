<?php

declare(strict_types=1);

namespace App\Core\Attribution;

enum ActorType: string
{
    case AuthenticatedUser = 'authenticated_user';
    case ImpersonatedUser = 'impersonated_user';
    case PublicAnonymous = 'public_anonymous';
    case PublicVerified = 'public_verified';
    case ApplicantAccess = 'applicant_access';
    case ApiUser = 'api_user';
    case ApiClient = 'api_client';
    case Integration = 'integration';
    case Webhook = 'webhook';
    case QueuedJob = 'queued_job';
    case ScheduledTask = 'scheduled_task';
    case ConsoleCommand = 'console_command';
    case BulkImport = 'bulk_import';
    case DataMigration = 'data_migration';
    case System = 'system';
    case LegacyUnknown = 'legacy_unknown';
}
