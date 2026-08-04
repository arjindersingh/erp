<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admissions\Pages;

use App\Filament\Resources\Admissions\AdmissionCampaignResource;
use Filament\Resources\Pages\ListRecords;

final class ListAdmissionCampaigns extends ListRecords
{
    protected static string $resource = AdmissionCampaignResource::class;
}
