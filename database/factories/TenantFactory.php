<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Tenant> */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company().' Education';

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'code' => strtoupper(fake()->unique()->bothify('???###')),
            'status' => 'active',
            'settings' => [],
        ];
    }
}
