<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum AffiliationStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Withdrawn = 'withdrawn';
}
