<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum AcademicYearLockStatus: string
{
    case Active = 'active';
    case Released = 'released';
    case Expired = 'expired';
}
