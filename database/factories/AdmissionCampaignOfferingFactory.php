<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Admissions\Models\AdmissionCampaign;
use App\Domains\Admissions\Models\AdmissionCampaignOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AdmissionCampaignOffering> */
final class AdmissionCampaignOfferingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id' => AdmissionCampaign::factory(),
            'tenant_id' => fn (array $a): int => (int) AdmissionCampaign::withoutGlobalScopes()->findOrFail($a['campaign_id'])->tenant_id,
            'offering_type' => 'programme',
            'preference_order' => 1,
            'is_active' => true,
            'settings' => [],
        ];
    }
}
