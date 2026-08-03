<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Modules\Module;
use App\Core\Modules\TenantModule;
use App\Core\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TenantModule> */
class TenantModuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'module_id' => Module::factory(),
            'is_enabled' => true,
            'enabled_at' => now(),
            'configuration_json' => [],
        ];
    }
}
