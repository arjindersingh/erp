<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Admissions\Models\AdmissionApplication;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AdmissionApplication> */
final class AdmissionApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id' => AdmissionCampaign::factory(),
            'tenant_id' => fn (array $a): int => (int) ($c = AdmissionCampaign::withoutGlobalScopes()->findOrFail($a['campaign_id']))->tenant_id,
            'company_id' => fn (array $a): int => (int) AdmissionCampaign::withoutGlobalScopes()->findOrFail($a['campaign_id'])->company_id,
            'campus_id' => fn (array $a): int => (int) AdmissionCampaign::withoutGlobalScopes()->findOrFail($a['campaign_id'])->campus_id,
            'institute_id' => fn (array $a): int => (int) AdmissionCampaign::withoutGlobalScopes()->findOrFail($a['campaign_id'])->institute_id,
            'academic_year_id' => fn (array $a): int => (int) AdmissionCampaign::withoutGlobalScopes()->findOrFail($a['campaign_id'])->academic_year_id,
            'source' => 'public_online',
            'applicant_given_name' => fake()->firstName(),
            'applicant_family_name' => fake()->lastName(),
            'applicant_email' => fake()->safeEmail(),
            'status' => 'draft',
        ];
    }
}
