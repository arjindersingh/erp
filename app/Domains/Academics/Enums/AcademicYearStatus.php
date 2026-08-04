<?php

declare(strict_types=1);

namespace App\Domains\Academics\Enums;

enum AcademicYearStatus: string
{
    case Draft = 'draft';
    case Planned = 'planned';
    case Open = 'open';
    case Active = 'active';
    case Locked = 'locked';
    case Closed = 'closed';
    case Archived = 'archived';

    public function isReadOnly(): bool
    {
        return in_array($this, [self::Locked, self::Closed, self::Archived], true);
    }
}
