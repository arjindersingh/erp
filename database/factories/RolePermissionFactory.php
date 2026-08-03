<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Authorization\Permission;
use App\Core\Authorization\Role;
use App\Core\Authorization\RolePermission;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RolePermission> */
class RolePermissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'role_id' => Role::factory(),
            'permission_id' => Permission::factory(),
            'granted_at' => now(),
            'status' => 'active',
        ];
    }
}
