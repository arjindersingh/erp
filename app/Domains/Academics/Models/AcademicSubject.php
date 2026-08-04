<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class AcademicSubject extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['credits' => 'decimal:2', 'maximum_marks' => 'decimal:2', 'passing_marks' => 'decimal:2', 'theory_hours' => 'integer', 'practical_hours' => 'integer'];
    }
}
