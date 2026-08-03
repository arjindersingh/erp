<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Authorization\RoleAssignment;
use App\Core\Authorization\RoleAssignmentStatus;
use App\Core\Identity\UserMembership;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<RoleAssignment> */
class RoleAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_membership_id' => UserMembership::factory(),
            'tenant_id' => fn (array $attributes): int => (int) UserMembership::query()
                ->findOrFail($attributes['user_membership_id'])->tenant_id,
            'user_id' => fn (array $attributes): int => (int) UserMembership::query()
                ->findOrFail($attributes['user_membership_id'])->user_id,
            'access_scope_id' => fn (array $attributes): int => (int) UserMembership::query()
                ->findOrFail($attributes['user_membership_id'])->access_scope_id,
            'role_id' => RoleFactory::new(),
            'is_primary' => false,
            'status' => RoleAssignmentStatus::Active,
            'approved_at' => now(),
        ];
    }
}
