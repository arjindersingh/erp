<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Academics\Models\EducationAuthority;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<EducationAuthority> */
final class EducationAuthorityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(), 'tenant_id' => null, 'authority_type' => 'other',
            'code' => strtoupper(fake()->unique()->bothify('AUTH-###')), 'name' => fake()->company(),
            'is_system' => true, 'status' => 'active',
        ];
    }
}
