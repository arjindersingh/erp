<?php

declare(strict_types=1);

namespace App\Domains\Students\Enums;

enum ProfileRecordStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Verified = 'verified';
    case Active = 'active';
    case Suspended = 'suspended';
    case Archived = 'archived';
    case Merged = 'merged';
}
