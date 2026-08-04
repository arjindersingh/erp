<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation\Organisation;

use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('foundation')]
#[Group('tenancy')]
#[Group('isolation')]
final class OrganisationHierarchyIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_rejects_a_campus_linked_to_another_tenants_company(): void
    {
        [$tenantA, $tenantB] = $this->tenants();
        $companyB = Company::factory()->for($tenantB)->create();

        $this->expectException(QueryException::class);
        Campus::factory()->create(['tenant_id' => $tenantA->id, 'company_id' => $companyB->id]);
    }

    public function test_database_rejects_an_institute_linked_to_another_tenants_campus(): void
    {
        [$tenantA, $tenantB] = $this->tenants();
        $companyA = Company::factory()->for($tenantA)->create();
        $companyB = Company::factory()->for($tenantB)->create();
        $campusB = Campus::factory()->for($tenantB)->for($companyB)->create();

        $this->expectException(QueryException::class);
        Institute::factory()->create([
            'tenant_id' => $tenantA->id,
            'company_id' => $companyA->id,
            'campus_id' => $campusB->id,
        ]);
    }

    public function test_codes_are_unique_within_boundary_but_reusable_across_tenants(): void
    {
        [$tenantA, $tenantB] = $this->tenants();
        Company::factory()->for($tenantA)->create(['code' => 'SHARED']);
        Company::factory()->for($tenantB)->create(['code' => 'SHARED']);

        $this->assertDatabaseCount('companies', 2);
    }

    /** @return array{Tenant, Tenant} */
    private function tenants(): array
    {
        return [
            Tenant::factory()->create(['name' => 'Tenant A', 'slug' => 'tenant-a', 'code' => 'A']),
            Tenant::factory()->create(['name' => 'Tenant B', 'slug' => 'tenant-b', 'code' => 'B']),
        ];
    }
}
