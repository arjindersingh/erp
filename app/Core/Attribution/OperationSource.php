<?php

declare(strict_types=1);

namespace App\Core\Attribution;

enum OperationSource: string
{
    case AdminPanel = 'admin_panel';
    case StaffPortal = 'staff_portal';
    case TeacherPortal = 'teacher_portal';
    case StudentPortal = 'student_portal';
    case GuardianPortal = 'guardian_portal';
    case ManagementPortal = 'management_portal';
    case ApplicantPortal = 'applicant_portal';
    case PublicWeb = 'public_web';
    case PublicApi = 'public_api';
    case AuthenticatedApi = 'authenticated_api';
    case MobileApp = 'mobile_app';
    case Console = 'console';
    case Queue = 'queue';
    case Scheduler = 'scheduler';
    case BulkImport = 'bulk_import';
    case Migration = 'migration';
    case Integration = 'integration';
    case Webhook = 'webhook';
    case System = 'system';
}
