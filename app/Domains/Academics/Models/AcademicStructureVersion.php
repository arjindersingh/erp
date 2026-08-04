<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class AcademicStructureVersion extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}
