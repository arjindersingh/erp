<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class Semester extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['number' => 'integer'];
    }
}
