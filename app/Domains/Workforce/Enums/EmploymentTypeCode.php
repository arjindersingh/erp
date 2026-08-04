<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Enums;

enum EmploymentTypeCode: string
{
    case Regular = 'regular';
    case Probation = 'probation';
    case Contract = 'contract';
    case PartTime = 'part_time';
    case Visiting = 'visiting';
    case GuestFaculty = 'guest_faculty';
    case Temporary = 'temporary';
    case DailyWage = 'daily_wage';
    case Consultant = 'consultant';
    case Deputation = 'deputation';
}
