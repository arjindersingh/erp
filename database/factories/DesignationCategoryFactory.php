<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Workforce\Models\DesignationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DesignationCategory> */
final class DesignationCategoryFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('DC-###')), 'name' => fake()->jobTitle(), 'sequence' => 10, 'is_system' => true, 'status' => 'active'];
    }
}
