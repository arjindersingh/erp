<?php

declare(strict_types=1);

namespace App\Domains\Academics\Contracts;

use App\Core\AcademicYear\AcademicYearContext;
use App\Domains\Academics\Models\AcademicSection;
use App\Domains\Academics\Models\SemesterOffering;
use Illuminate\Support\Collection;

interface SubjectOfferingProvider
{
    public function forSection(AcademicSection $section, AcademicYearContext $context): Collection;

    public function forSemester(SemesterOffering $semester, AcademicYearContext $context): Collection;
}
