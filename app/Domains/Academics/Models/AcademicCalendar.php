<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class AcademicCalendar extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }
}
