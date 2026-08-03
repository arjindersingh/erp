<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\IdentityStatus;
use App\Core\Identity\Person;
use App\Core\Identity\Profile;
use App\Core\Identity\ProfileType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Profile> */
class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'person_id' => Person::factory(),
            'tenant_id' => fn (array $attributes): int => (int) Person::query()
                ->findOrFail($attributes['person_id'])->tenant_id,
            'type' => fake()->randomElement(ProfileType::cases()),
            'display_name' => null,
            'reference_number' => null,
            'status' => IdentityStatus::Active,
            'is_primary' => false,
            'metadata' => [],
        ];
    }
}
