<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\Person;
use App\Domains\Workforce\Models\EmployeeProfile;
use App\Domains\Workforce\Models\EmploymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EmployeeProfile> */
final class EmployeeProfileFactory extends Factory
{
    public function definition(): array
    {
        return ['person_id' => Person::factory(), 'tenant_id' => fn (array $a) => Person::withoutGlobalScopes()->findOrFail($a['person_id'])->tenant_id, 'employee_number' => strtoupper(fake()->unique()->bothify('EMP-####')), 'joining_date' => now()->subYear()->toDateString(), 'employment_status_id' => EmploymentStatus::factory(), 'status' => 'active'];
    }
}
