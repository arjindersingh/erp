<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\IdentityStatus;
use App\Core\Identity\Person;
use App\Core\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Person> */
class PersonFactory extends Factory
{
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'first_name' => $firstName,
            'middle_name' => null,
            'last_name' => $lastName,
            'display_name' => $firstName.' '.$lastName,
            'date_of_birth' => fake()->optional()->dateTimeBetween('-70 years', '-5 years'),
            'primary_email' => fake()->optional()->safeEmail(),
            'primary_mobile' => null,
            'status' => IdentityStatus::Active,
            'metadata' => [],
        ];
    }
}
