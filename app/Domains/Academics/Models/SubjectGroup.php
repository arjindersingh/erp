<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

final class SubjectGroup extends CanonicalAcademicModel
{
    protected function casts(): array
    {
        return ['minimum_selections' => 'integer', 'maximum_selections' => 'integer'];
    }
}
