<?php

declare(strict_types=1);

namespace App\Domains\Academics\Contracts;

use App\Domains\Academics\Models\ProgrammeOffering;
use Illuminate\Support\Collection;

interface ProgrammeSemesterProvider
{
    public function semesterOfferingsForProgramme(ProgrammeOffering $offering): Collection;
}
