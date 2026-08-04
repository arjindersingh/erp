<?php

declare(strict_types=1);

namespace Tests\Feature\Academics\Foundation;

use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Domains\Academics\Models\EducationAuthority;
use App\Domains\Academics\Models\EducationLevel;
use App\Domains\Academics\Models\InstituteAuthorityAffiliation;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('academics')]
#[Group('foundation')]
#[Group('isolation')]
final class AcademicCatalogueIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogues_expose_platform_records_and_only_the_active_tenants_records(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        EducationLevel::factory()->create(['code' => 'SYSTEM', 'name' => 'System', 'tenant_id' => null, 'is_system' => true]);
        EducationLevel::factory()->create(['code' => 'A-LEVEL', 'name' => 'Tenant A Level', 'tenant_id' => $tenantA->id, 'is_system' => false]);
        EducationLevel::factory()->create(['code' => 'B-LEVEL', 'name' => 'Tenant B Level', 'tenant_id' => $tenantB->id, 'is_system' => false]);
        app(TenantContext::class)->activate($tenantA);

        $this->assertEqualsCanonicalizing(['System', 'Tenant A Level'], EducationLevel::query()->pluck('name')->all());
    }

    public function test_an_affiliation_rejects_an_authority_owned_by_another_tenant(): void
    {
        $instituteA = Institute::factory()->create();
        $tenantA = Tenant::query()->findOrFail($instituteA->tenant_id);
        $tenantB = Tenant::factory()->create();
        $authorityB = EducationAuthority::factory()->create(['tenant_id' => $tenantB->id, 'is_system' => false]);

        $this->expectException(ValidationException::class);
        InstituteAuthorityAffiliation::factory()->create([
            'institute_id' => $instituteA->id, 'tenant_id' => $tenantA->id,
            'company_id' => $instituteA->company_id, 'campus_id' => $instituteA->campus_id,
            'education_authority_id' => $authorityB->id,
        ]);
    }

    public function test_platform_catalogue_codes_are_database_unique(): void
    {
        EducationLevel::factory()->create(['tenant_id' => null, 'is_system' => true, 'code' => 'PRIMARY']);

        $this->expectException(QueryException::class);
        EducationLevel::factory()->create(['tenant_id' => null, 'is_system' => true, 'code' => 'PRIMARY']);
    }
}
