<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum AcademicRecordStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';
}
