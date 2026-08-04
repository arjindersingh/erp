<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation\Isolation;

use App\Core\Organization\Company;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('foundation')]
#[Group('tenancy')]
#[Group('isolation')]
final class TenantQueryScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisation_queries_are_scoped_to_the_active_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $companyA = Company::factory()->for($tenantA)->create(['name' => 'Company A']);
        $companyB = Company::factory()->for($tenantB)->create(['name' => 'Company B']);

        app(TenantContext::class)->activate($tenantA);

        $this->assertSame([$companyA->id], Company::query()->pluck('id')->all());
        $this->assertNull(Company::query()->find($companyB->id));
        $this->assertSame(2, Company::withoutGlobalScopes()->count());
    }

    public function test_switching_context_does_not_reuse_the_previous_tenant_scope(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        Company::factory()->for($tenantA)->create(['name' => 'Company A']);
        Company::factory()->for($tenantB)->create(['name' => 'Company B']);
        $context = app(TenantContext::class);

        $context->activate($tenantA);
        $this->assertSame(['Company A'], Company::query()->pluck('name')->all());
        $context->activate($tenantB);
        $this->assertSame(['Company B'], Company::query()->pluck('name')->all());
    }
}
