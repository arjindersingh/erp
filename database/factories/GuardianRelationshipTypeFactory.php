<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Students\Models\GuardianRelationshipType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GuardianRelationshipType> */
final class GuardianRelationshipTypeFactory extends Factory
{
    public function definition(): array
    {
        return ['tenant_id' => null, 'code' => strtoupper(fake()->unique()->bothify('GRT-###')), 'name' => fake()->word(), 'is_parent_relationship' => false, 'is_legal_relationship' => false, 'is_system' => true, 'status' => 'active'];
    }
}
