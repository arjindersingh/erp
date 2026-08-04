<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class AcademicProgramme extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['duration_months' => 'integer', 'required_credits' => 'decimal:2'];
    }
}
