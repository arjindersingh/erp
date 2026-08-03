<?php

namespace Tests\Feature\Core;

use App\Core\Authorization\AccessScope;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Organization\InstituteType;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantDomain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_organization_hierarchy_can_be_created(): void
    {
        $tenant = Tenant::create([
            'name' => 'Acme Education',
            'slug' => 'acme-education',
            'code' => 'ACME',
        ]);

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name' => 'Acme Education Trust',
            'slug' => 'acme-trust',
            'code' => 'TRUST',
            'type' => 'trust',
        ]);

        $campus = Campus::create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'name' => 'Main Campus',
            'slug' => 'main-campus',
            'code' => 'MAIN',
        ]);

        $instituteType = InstituteType::create([
            'tenant_id' => $tenant->id,
            'name' => 'College',
            'slug' => 'college',
            'code' => 'COLLEGE',
        ]);

        $institute = Institute::create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'campus_id' => $campus->id,
            'institute_type_id' => $instituteType->id,
            'name' => 'Acme College of Arts',
            'slug' => 'arts-college',
            'code' => 'ARTS',
        ]);

        TenantDomain::create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme.example.test',
            'is_primary' => true,
        ]);

        $tenantScope = AccessScope::factory()->for($tenant)->create([
            'name' => $tenant->name,
            'code' => 'TENANT',
        ]);
        $companyScope = AccessScope::factory()->forCompany($company, $tenantScope)->create();
        $campusScope = AccessScope::factory()->forCampus($campus, $companyScope)->create();
        $scope = AccessScope::factory()->forInstitute($institute, $campusScope)->create();

        $this->assertTrue($tenant->companies()->whereKey($company)->exists());
        $this->assertTrue($company->campuses()->whereKey($campus)->exists());
        $this->assertTrue($campus->institutes()->whereKey($institute)->exists());
        $this->assertTrue($tenant->domains()->where('domain', 'acme.example.test')->exists());
        $this->assertTrue($institute->accessScopes()->whereKey($scope)->exists());
        $this->assertTrue($tenantScope->isAncestorOf($scope));
        $this->assertTrue($scope->isDescendantOf($campusScope));
    }
}
