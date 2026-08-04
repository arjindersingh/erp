<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\GuardianProfileResource;
use Filament\Resources\Pages\ListRecords;

final class ListGuardianProfiles extends ListRecords
{
    protected static string $resource = GuardianProfileResource::class;
}
