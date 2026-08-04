<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admissions\Pages;

use App\Filament\Resources\Admissions\AdmissionApplicationResource;
use Filament\Resources\Pages\ListRecords;

final class ListAdmissionApplications extends ListRecords
{
    protected static string $resource = AdmissionApplicationResource::class;
}
