<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Students\Models\StudentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StudentCategory> */
final class StudentCategoryFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('SC-###')), 'name' => fake()->words(2, true), 'category_type' => 'other', 'is_system' => true, 'status' => 'active'];
    }
}
