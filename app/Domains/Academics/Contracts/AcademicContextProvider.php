<?php

declare(strict_types=1);

namespace App\Domains\Academics\Contracts;

use App\Core\AcademicYear\AcademicYearContext;
use Illuminate\Support\Collection;

interface AcademicContextProvider
{
    public function classesForContext(AcademicYearContext $context): Collection;

    public function programmesForContext(AcademicYearContext $context): Collection;
}
