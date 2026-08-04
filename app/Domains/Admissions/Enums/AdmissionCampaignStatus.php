<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Enums;

enum AdmissionCampaignStatus: string
{
    case Draft = 'draft';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Scheduled = 'scheduled';
    case Open = 'open';
    case Paused = 'paused';
    case Closed = 'closed';
    case SelectionInProgress = 'selection_in_progress';
    case AdmissionInProgress = 'admission_in_progress';
    case Completed = 'completed';
    case Archived = 'archived';
}
