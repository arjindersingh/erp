<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<TenantSubscription> */
class TenantSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'plan_code' => 'foundation',
            'status' => 'trial',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(30),
            'limits' => [],
            'features' => ['core', 'organization', 'identity', 'access', 'audit'],
        ];
    }
}
