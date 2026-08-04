<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Organization\Institute;
use App\Domains\Workforce\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Department> */
final class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return ['institute_id' => Institute::factory(), 'campus_id' => fn (array $a) => Institute::withoutGlobalScopes()->findOrFail($a['institute_id'])->campus_id, 'company_id' => fn (array $a) => Institute::withoutGlobalScopes()->findOrFail($a['institute_id'])->company_id, 'tenant_id' => fn (array $a) => Institute::withoutGlobalScopes()->findOrFail($a['institute_id'])->tenant_id, 'code' => strtoupper(fake()->unique()->bothify('DEP-###')), 'name' => fake()->words(2, true), 'department_type' => 'academic', 'display_order' => 10, 'status' => 'active'];
    }
}
