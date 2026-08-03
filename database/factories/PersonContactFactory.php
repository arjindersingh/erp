<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\ContactType;
use App\Core\Identity\Person;
use App\Core\Identity\PersonContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PersonContact> */
class PersonContactFactory extends Factory
{
    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();

        return [
            'person_id' => Person::factory(),
            'tenant_id' => fn (array $attributes): int => (int) Person::query()
                ->findOrFail($attributes['person_id'])->tenant_id,
            'type' => ContactType::Email,
            'label' => 'personal',
            'value' => $email,
            'normalized_value' => mb_strtolower($email),
            'is_primary' => false,
            'is_verified' => false,
        ];
    }
}
