<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Authorization\AccessScope;
use App\Core\Modules\Module;
use App\Core\Modules\TenantModule;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantDomain;
use Illuminate\Database\Seeder;

final class UatOrganisationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['UAT-A' => 'Tenant A Education', 'UAT-B' => 'Tenant B Education'] as $code => $name) {
            $tenant = Tenant::query()->updateOrCreate(['code' => $code], ['name' => $name, 'slug' => strtolower($code), 'status' => 'active']);
            TenantDomain::withoutGlobalScopes()->updateOrCreate(['domain' => strtolower($code).'.erp-uat.test'], ['tenant_id' => $tenant->id, 'domain_type' => 'custom', 'status' => 'active', 'is_primary' => true, 'is_verified' => true, 'verified_at' => now()]);
            $admissions = Module::query()->where('code', 'admissions')->firstOrFail();
            TenantModule::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'module_id' => $admissions->id], ['is_enabled' => true, 'enabled_at' => now()]);
            $company = Company::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'code' => $code.'-CO'], ['name' => "{$name} Company", 'slug' => strtolower($code).'-company', 'status' => 'active']);
            $campus = Campus::withoutGlobalScopes()->updateOrCreate(['company_id' => $company->id, 'code' => $code.'-CA'], ['tenant_id' => $tenant->id, 'name' => "{$name} Campus", 'slug' => strtolower($code).'-campus', 'status' => 'active']);
            $tenantScope = AccessScope::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'code' => $code.'-SCOPE'], ['scope_type' => 'tenant', 'name' => $name, 'status' => 'active']);
            $companyScope = AccessScope::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'code' => $code.'-CO-SCOPE'], ['parent_scope_id' => $tenantScope->id, 'scope_type' => 'company', 'company_id' => $company->id, 'name' => $company->name, 'status' => 'active']);
            $campusScope = AccessScope::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'code' => $code.'-CA-SCOPE'], ['parent_scope_id' => $companyScope->id, 'scope_type' => 'campus', 'company_id' => $company->id, 'campus_id' => $campus->id, 'name' => $campus->name, 'status' => 'active']);
            foreach (['SCHOOL' => 'School', 'COLLEGE' => 'College'] as $suffix => $label) {
                $institute = Institute::withoutGlobalScopes()->updateOrCreate(['campus_id' => $campus->id, 'code' => $code.'-'.$suffix], ['tenant_id' => $tenant->id, 'company_id' => $company->id, 'name' => "{$label} {$code}", 'slug' => strtolower($code.'-'.$suffix), 'status' => 'active']);
                AccessScope::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'code' => $code.'-'.$suffix.'-SCOPE'], ['parent_scope_id' => $campusScope->id, 'scope_type' => 'institute', 'company_id' => $company->id, 'campus_id' => $campus->id, 'institute_id' => $institute->id, 'name' => $institute->name, 'status' => 'active']);
            }
        }
    }
}
