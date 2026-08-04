<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation\Tenancy;

use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Core\Tenancy\TenantDomain;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('foundation')]
#[Group('tenancy')]
#[Group('isolation')]
final class TenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware([ResolvePublicTenant::class, EnsureTenantIsActive::class])->get('/_foundation/resolved-tenant', function (TenantContext $context) {
            return response()->json(['tenant_id' => $context->id()]);
        });
    }

    public function test_verified_domains_resolve_two_separate_tenants_and_never_trust_browser_tenant_id(): void
    {
        [$tenantA, $tenantB] = $this->twoTenants();

        $this->getJson('http://a.erp.test/_foundation/resolved-tenant?tenant_id='.$tenantB->id)
            ->assertOk()->assertJson(['tenant_id' => $tenantA->id]);

        $this->getJson('http://b.erp.test/_foundation/resolved-tenant?tenant_id='.$tenantA->id)
            ->assertOk()->assertJson(['tenant_id' => $tenantB->id]);
    }

    public function test_unknown_unverified_and_inactive_domains_cannot_activate_a_tenant(): void
    {
        $tenant = Tenant::factory()->create(['name' => 'Tenant A', 'slug' => 'tenant-a', 'code' => 'A']);
        TenantDomain::factory()->for($tenant)->unverified()->create(['domain' => 'pending.erp.test']);

        $this->getJson('http://unknown.erp.test/_foundation/resolved-tenant')->assertNotFound();
        $this->getJson('http://pending.erp.test/_foundation/resolved-tenant')->assertNotFound();

        $tenant->update(['status' => 'suspended']);
        TenantDomain::factory()->for($tenant)->create(['domain' => 'suspended.erp.test']);
        $this->getJson('http://suspended.erp.test/_foundation/resolved-tenant')->assertForbidden();
    }

    /** @return array{Tenant, Tenant} */
    private function twoTenants(): array
    {
        $tenantA = Tenant::factory()->create(['name' => 'Tenant A', 'slug' => 'tenant-a', 'code' => 'A']);
        $tenantB = Tenant::factory()->create(['name' => 'Tenant B', 'slug' => 'tenant-b', 'code' => 'B']);
        TenantDomain::factory()->for($tenantA)->create(['domain' => 'a.erp.test']);
        TenantDomain::factory()->for($tenantB)->create(['domain' => 'b.erp.test']);

        return [$tenantA, $tenantB];
    }
}
