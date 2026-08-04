<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum EducationLevelCategory: string
{
    case PrePrimary = 'pre_primary';
    case Primary = 'primary';
    case Middle = 'middle';
    case Secondary = 'secondary';
    case SeniorSecondary = 'senior_secondary';
    case Certificate = 'certificate';
    case Diploma = 'diploma';
    case Undergraduate = 'undergraduate';
    case Postgraduate = 'postgraduate';
    case Doctoral = 'doctoral';
    case Professional = 'professional';
    case Vocational = 'vocational';
    case Other = 'other';
}
