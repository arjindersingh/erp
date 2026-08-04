<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class AcademicTerm extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date'];
    }
}
