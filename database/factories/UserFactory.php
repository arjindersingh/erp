<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\AccountStatus;
use App\Core\Identity\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->name(),
            'account_type' => AccountType::Person,
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => null,
            'status' => AccountStatus::Active,
            'email_verified_at' => now(),
            'mobile_verified_at' => null,
            'password' => static::$password ??= Hash::make('password'),
            'failed_login_attempts' => 0,
            'must_change_password' => false,
            'password_changed_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountStatus::Pending,
            'email_verified_at' => null,
            'password' => null,
        ]);
    }

    public function siteAdministrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_type' => AccountType::SiteAdmin,
        ]);
    }
}
