<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Workforce\Models\EmploymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EmploymentType> */
final class EmploymentTypeFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('ET-###')), 'name' => fake()->word(), 'is_system' => true, 'status' => 'active'];
    }
}
