<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Authorization\AccessScope;
use App\Core\Identity\MembershipStatus;
use App\Core\Identity\MembershipType;
use App\Core\Identity\UserMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<UserMembership> */
class UserMembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'person_id' => null,
            'profile_id' => null,
            'access_scope_id' => AccessScope::factory(),
            'tenant_id' => fn (array $attributes): int => (int) AccessScope::query()
                ->findOrFail($attributes['access_scope_id'])->tenant_id,
            'membership_type' => MembershipType::Service,
            'is_primary' => false,
            'status' => MembershipStatus::Active,
            'approved_at' => now(),
            'remarks' => null,
            'metadata' => [],
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MembershipStatus::Pending,
        ]);
    }
}
