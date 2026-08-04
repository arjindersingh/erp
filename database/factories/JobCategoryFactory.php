<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Workforce\Models\JobCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<JobCategory> */
final class JobCategoryFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('JC-###')), 'name' => fake()->words(2, true), 'is_system' => true, 'status' => 'active'];
    }
}
