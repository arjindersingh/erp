<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantDomain;
use App\Core\Tenancy\TenantSubscription;
use Illuminate\Database\Seeder;

final class TenantFoundationSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $tenant = Tenant::query()->firstOrCreate(
            ['slug' => 'demo-education-group'],
            [
                'name' => 'Demo Education Group', 'legal_name' => 'Demo Education Group', 'code' => 'DEMO',
                'status' => 'active', 'timezone' => 'Asia/Kolkata', 'locale' => 'en', 'currency' => 'INR',
                'branding' => ['primary_colour' => '#0e7490'], 'settings' => [],
            ],
        );
        TenantDomain::query()->firstOrCreate(
            ['domain' => 'demo.erp.test'],
            ['tenant_id' => $tenant->id, 'domain_type' => 'custom', 'status' => 'active', 'is_primary' => true, 'is_verified' => true, 'verified_at' => now()],
        );
        TenantSubscription::query()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'plan_code' => 'foundation'],
            ['status' => 'trial', 'starts_at' => now(), 'trial_ends_at' => now()->addDays(30), 'limits' => [], 'features' => ['core', 'organization', 'identity', 'access', 'audit']],
        );
    }
}
