<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Academics\Models\EducationLevel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<EducationLevel> */
final class EducationLevelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(), 'tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('LVL-###')),
            'name' => fake()->words(2, true), 'level_category' => 'other', 'sequence' => 1,
            'is_system' => true, 'status' => 'active',
        ];
    }
}
