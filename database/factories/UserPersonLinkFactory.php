<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\IdentityStatus;
use App\Core\Identity\Person;
use App\Core\Identity\UserPersonLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserPersonLink> */
class UserPersonLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'person_id' => Person::factory(),
            'tenant_id' => fn (array $attributes): int => (int) Person::query()
                ->findOrFail($attributes['person_id'])->tenant_id,
            'is_primary' => false,
            'status' => IdentityStatus::Active,
        ];
    }
}
