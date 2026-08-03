<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Organization\Company;
use App\Core\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Company> */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company().' Trust';

        return [
            'tenant_id' => Tenant::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'code' => strtoupper(fake()->unique()->bothify('CO-####')),
            'type' => 'trust',
            'status' => 'active',
            'settings' => [],
        ];
    }
}
