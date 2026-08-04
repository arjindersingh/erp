<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Organization\Institute;
use App\Domains\Academics\Models\EducationAuthority;
use App\Domains\Academics\Models\InstituteAuthorityAffiliation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<InstituteAuthorityAffiliation> */
final class InstituteAuthorityAffiliationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(), 'institute_id' => Institute::factory(),
            'tenant_id' => fn (array $attributes): int => (int) Institute::withoutGlobalScopes()->findOrFail($attributes['institute_id'])->tenant_id,
            'company_id' => fn (array $attributes): int => (int) Institute::withoutGlobalScopes()->findOrFail($attributes['institute_id'])->company_id,
            'campus_id' => fn (array $attributes): int => (int) Institute::withoutGlobalScopes()->findOrFail($attributes['institute_id'])->campus_id,
            'education_authority_id' => EducationAuthority::factory(), 'affiliation_type' => 'recognition',
            'status' => 'active', 'is_primary' => true,
        ];
    }
}
