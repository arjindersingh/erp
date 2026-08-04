<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum EducationAuthorityType: string
{
    case SchoolBoard = 'school_board';
    case University = 'university';
    case RegulatoryBody = 'regulatory_body';
    case AccreditationBody = 'accreditation_body';
    case ProfessionalCouncil = 'professional_council';
    case GovernmentDepartment = 'government_department';
    case AutonomousBody = 'autonomous_body';
    case Other = 'other';
}
