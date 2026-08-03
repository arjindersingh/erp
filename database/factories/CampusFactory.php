<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Campus> */
class CampusFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->city().' Campus';

        return [
            'company_id' => Company::factory(),
            'tenant_id' => fn (array $attributes): int => (int) Company::query()
                ->findOrFail($attributes['company_id'])->tenant_id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'code' => strtoupper(fake()->unique()->bothify('CA-####')),
            'status' => 'active',
            'settings' => [],
        ];
    }
}
