<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Enums;

enum DepartmentType: string
{
    case Academic = 'academic';
    case Administrative = 'administrative';
    case Finance = 'finance';
    case HumanResource = 'human_resource';
    case Examination = 'examination';
    case Admission = 'admission';
    case Library = 'library';
    case Transport = 'transport';
    case Placement = 'placement';
    case Research = 'research';
    case StudentWelfare = 'student_welfare';
    case SupportService = 'support_service';
    case Other = 'other';
}
