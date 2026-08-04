<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentProfileResource;
use Filament\Resources\Pages\ListRecords;

final class ListStudentProfiles extends ListRecords
{
    protected static string $resource = StudentProfileResource::class;
}
