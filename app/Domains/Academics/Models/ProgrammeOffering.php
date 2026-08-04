<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class ProgrammeOffering extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['intake_capacity' => 'integer'];
    }
}
