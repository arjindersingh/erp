<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantDomain;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<TenantDomain> */
class TenantDomainFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'domain' => fake()->unique()->domainName(),
            'domain_type' => 'custom',
            'status' => 'active',
            'is_primary' => true,
            'is_verified' => true,
            'verified_at' => now(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (): array => ['status' => 'pending', 'is_verified' => false, 'verified_at' => null]);
    }
}
