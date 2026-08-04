<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Workforce\Models\EmploymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EmploymentStatus> */
final class EmploymentStatusFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('ES-###')), 'name' => fake()->word(), 'is_active_status' => true, 'is_terminal_status' => false, 'is_system' => true, 'status' => 'active'];
    }

    public function inactive(): static
    {
        return $this->state(['is_active_status' => false, 'is_terminal_status' => true]);
    }
}
