<?php

declare(strict_types=1);

namespace App\Core\Attribution;

enum AuditEventCategory: string
{
    case Authentication = 'authentication';
    case Authorisation = 'authorisation';
    case RecordLifecycle = 'record_lifecycle';
    case Workflow = 'workflow';
    case Approval = 'approval';
    case Security = 'security';
    case PublicSubmission = 'public_submission';
    case Integration = 'integration';
    case Communication = 'communication';
    case FileAccess = 'file_access';
    case DataExport = 'data_export';
    case DataImport = 'data_import';
    case Configuration = 'configuration';
    case Impersonation = 'impersonation';
    case System = 'system';
    case Privacy = 'privacy';
}
