<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Organization\Institute;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AdmissionCampaign> */
final class AdmissionCampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institute_id' => Institute::factory(),
            'tenant_id' => fn (array $a): int => (int) Institute::withoutGlobalScopes()->findOrFail($a['institute_id'])->tenant_id,
            'company_id' => fn (array $a): int => (int) Institute::withoutGlobalScopes()->findOrFail($a['institute_id'])->company_id,
            'campus_id' => fn (array $a): int => (int) Institute::withoutGlobalScopes()->findOrFail($a['institute_id'])->campus_id,
            'academic_year_id' => fn (array $a): int => AcademicYear::factory()->create(['tenant_id' => $a['tenant_id']])->id,
            'code' => strtoupper(fake()->unique()->bothify('ADM-####')),
            'name' => fake()->sentence(3),
            'application_opens_at' => now()->subDay(),
            'application_closes_at' => now()->addMonth(),
            'submission_deadline_at' => now()->addMonth(),
            'status' => 'open',
            'settings' => [],
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (): array => ['status' => 'closed', 'application_closes_at' => now()->subDay()]);
    }
}
