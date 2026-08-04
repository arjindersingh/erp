<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Enums;

enum EmploymentStatusCode: string
{
    case Active = 'active';
    case OnProbation = 'on_probation';
    case OnLeave = 'on_leave';
    case Suspended = 'suspended';
    case Transferred = 'transferred';
    case Resigned = 'resigned';
    case Retired = 'retired';
    case Terminated = 'terminated';
    case Deceased = 'deceased';
    case ContractCompleted = 'contract_completed';
}
