<?php

declare(strict_types=1);

namespace App\Domains\Admissions\Enums;

enum AdmissionOfferingType: string
{
    case SchoolClass = 'school_class';
    case Programme = 'programme';
}
