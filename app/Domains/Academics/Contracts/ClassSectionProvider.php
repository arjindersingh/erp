<?php

declare(strict_types=1);

namespace App\Domains\Academics\Contracts;

use App\Core\AcademicYear\AcademicYearContext;
use App\Domains\Academics\Models\AcademicClass;
use Illuminate\Support\Collection;

interface ClassSectionProvider
{
    public function sectionsForClass(AcademicClass $class, AcademicYearContext $context): Collection;
}
