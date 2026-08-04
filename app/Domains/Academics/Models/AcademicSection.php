<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class AcademicSection extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['capacity' => 'integer'];
    }
}
