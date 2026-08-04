<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Workforce\Models\Department;
use App\Domains\Workforce\Models\Designation;
use App\Domains\Workforce\Models\JobCategory;
use App\Domains\Workforce\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<JobPost> */
final class JobPostFactory extends Factory
{
    public function definition(): array
    {
        return ['department_id' => Department::factory(), 'tenant_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->tenant_id, 'company_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->company_id, 'campus_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->campus_id, 'institute_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->institute_id, 'designation_id' => fn (array $a) => Designation::factory()->create(['tenant_id' => $a['tenant_id']])->id, 'job_category_id' => JobCategory::factory(), 'code' => strtoupper(fake()->unique()->bothify('POST-###')), 'name' => fake()->jobTitle(), 'sanctioned_strength' => 1, 'filled_strength' => 0, 'status' => 'active'];
    }
}
