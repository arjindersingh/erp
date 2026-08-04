<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Enums;

enum EmploymentAssignmentAction: string
{
    case Appointment = 'appointment';
    case Transfer = 'transfer';
    case Promotion = 'promotion';
    case DesignationChange = 'designation_change';
    case DepartmentChange = 'department_change';
    case InstituteChange = 'institute_change';
    case StatusChange = 'status_change';
    case Extension = 'extension';
    case Completion = 'completion';
    case Termination = 'termination';
}
