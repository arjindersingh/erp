<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Enums;

enum AdmissionApplicationSource: string
{
    case PublicOnline = 'public_online';
    case ApplicantPortal = 'applicant_portal';
    case ManualPaper = 'manual_paper';
    case AssistedCounter = 'assisted_counter';
    case TelephoneAssisted = 'telephone_assisted';
    case OutreachCamp = 'outreach_camp';
    case BulkImport = 'bulk_import';
    case InternalTransfer = 'internal_transfer';
    case Readmission = 'readmission';
    case ManagementEntry = 'management_entry';
    case GovernmentNomination = 'government_nomination';
    case ExternalPortal = 'external_portal';
}
