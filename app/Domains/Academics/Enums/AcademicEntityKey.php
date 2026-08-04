<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum AcademicEntityKey: string
{
    case AcademicYear = 'academic_year';
    case Programme = 'programme';
    case Course = 'course';
    case ClassModel = 'class';
    case Section = 'section';
    case Subject = 'subject';
    case Term = 'term';
    case Semester = 'semester';
}
