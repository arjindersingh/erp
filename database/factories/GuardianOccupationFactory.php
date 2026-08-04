<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Students\Models\GuardianOccupation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GuardianOccupation> */
final class GuardianOccupationFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('GO-###')), 'name' => fake()->jobTitle(), 'is_system' => true, 'status' => 'active'];
    }
}
