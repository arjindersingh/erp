<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Authorization\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Role> */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'tenant_id' => null,
            'name' => fake()->unique()->slug(2),
            'code' => fake()->unique()->slug(2),
            'guard_name' => 'web',
            'role_type' => 'staff',
            'is_system' => false,
            'is_editable' => true,
            'is_assignable' => true,
            'status' => 'active',
        ];
    }
}
