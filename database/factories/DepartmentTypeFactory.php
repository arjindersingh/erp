<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Workforce\Models\DepartmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DepartmentType> */
final class DepartmentTypeFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('DT-###')), 'name' => fake()->words(2, true), 'is_academic' => false, 'is_system' => true, 'status' => 'active'];
    }
}
