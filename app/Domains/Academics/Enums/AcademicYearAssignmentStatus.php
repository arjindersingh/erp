<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum AcademicYearAssignmentStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Revoked = 'revoked';
}
