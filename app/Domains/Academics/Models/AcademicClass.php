<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class AcademicClass extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['sequence' => 'integer'];
    }
}
