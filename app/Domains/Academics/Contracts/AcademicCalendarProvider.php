<?php

declare(strict_types=1);

namespace App\Domains\Academics\Contracts;

use App\Core\AcademicYear\AcademicYearContext;
use Illuminate\Support\Collection;

interface AcademicCalendarProvider
{
    public function forContext(AcademicYearContext $context): Collection;
}
