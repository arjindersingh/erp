<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Organization\Campus;
use App\Core\Organization\Institute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Institute> */
class InstituteFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company().' School';

        return [
            'campus_id' => Campus::factory(),
            'company_id' => fn (array $attributes): int => (int) Campus::query()
                ->findOrFail($attributes['campus_id'])->company_id,
            'tenant_id' => fn (array $attributes): int => (int) Campus::query()
                ->findOrFail($attributes['campus_id'])->tenant_id,
            'institute_type_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'code' => strtoupper(fake()->unique()->bothify('IN-####')),
            'status' => 'active',
            'settings' => [],
        ];
    }
}
