<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class ClassSubjectMapping extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['credits' => 'decimal:2', 'maximum_marks' => 'decimal:2', 'passing_marks' => 'decimal:2'];
    }
}
