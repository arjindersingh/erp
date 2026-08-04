<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Students\Models\StudentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StudentStatus> */
final class StudentStatusFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('SS-###')), 'name' => fake()->word(), 'is_active_status' => true, 'is_terminal_status' => false, 'allows_enrolment' => true, 'allows_portal_access' => true, 'allows_financial_activity' => true, 'is_system' => true, 'status' => 'active'];
    }
}
