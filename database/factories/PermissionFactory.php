<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Authorization\Permission;
use App\Core\Modules\Module;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Permission> */
class PermissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(['code' => 'transport', 'name' => 'Transport']),
            'uuid' => (string) Str::uuid(),
            'name' => 'transport.routes.view',
            'code' => 'transport.routes.view',
            'guard_name' => 'web',
            'command' => 'view',
            'permission_type' => 'resource',
            'is_system' => true,
            'status' => 'active',
        ];
    }
}
