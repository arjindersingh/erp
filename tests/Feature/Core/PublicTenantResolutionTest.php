<?php

namespace Tests\Feature\Core;

use App\Core\Tenancy\CurrentTenant;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantDomain;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicTenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_request_resolves_tenant_from_domain(): void
    {
        Route::middleware(ResolvePublicTenant::class)->get('/tenant-resolution-test', function (CurrentTenant $currentTenant) {
            return response()->json([
                'tenant_id' => $currentTenant->id(),
                'tenant_slug' => $currentTenant->tenant()?->slug,
                'domain' => $currentTenant->domain()?->domain,
            ]);
        });

        $tenant = Tenant::create([
            'name' => 'Acme Education',
            'slug' => 'acme-education',
            'code' => 'ACME',
        ]);

        TenantDomain::create([
            'tenant_id' => $tenant->id,
            'domain' => 'school.example.test',
            'is_primary' => true,
            'is_verified' => true,
        ]);

        $response = $this
            ->getJson('http://school.example.test/tenant-resolution-test');

        $response
            ->assertOk()
            ->assertJson([
                'tenant_id' => $tenant->id,
                'tenant_slug' => 'acme-education',
                'domain' => 'school.example.test',
            ]);
    }

    public function test_unknown_public_domain_returns_not_found(): void
    {
        $response = $this
            ->getJson('http://unknown.example.test/api/health');

        $response
            ->assertNotFound()
            ->assertJson([
                'message' => 'Tenant could not be resolved for this domain.',
            ]);
    }

    public function test_configured_central_domain_can_run_without_tenant(): void
    {
        $response = $this
            ->getJson('http://localhost/api/health');

        $response->assertOk();
    }
}
