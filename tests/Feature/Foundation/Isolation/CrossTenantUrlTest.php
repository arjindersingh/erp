<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation\Isolation;

use App\Core\Organization\Company;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantDomain;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('foundation')]
#[Group('isolation')]
#[Group('security')]
final class CrossTenantUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_foreign_internal_id_and_uuid_style_route_value_are_not_discoverable(): void
    {
        Route::middleware([ResolvePublicTenant::class, EnsureTenantIsActive::class])
            ->get('/_foundation/companies/{company}', function (string $company) {
                return Company::query()
                    ->where(fn ($query) => $query->whereKey($company)->orWhere('slug', $company))
                    ->firstOrFail();
            });

        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        TenantDomain::factory()->for($tenantA)->create(['domain' => 'a-url.erp.test']);
        $companyB = Company::factory()->for($tenantB)->create(['slug' => 'tenant-b-private']);

        $this->getJson("http://a-url.erp.test/_foundation/companies/{$companyB->id}")->assertNotFound();
        $this->getJson('http://a-url.erp.test/_foundation/companies/tenant-b-private')->assertNotFound();
    }
}
