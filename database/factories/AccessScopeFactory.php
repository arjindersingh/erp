<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Authorization\AccessScope;
use App\Core\Authorization\ScopeType;
use App\Core\Identity\IdentityStatus;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<AccessScope> */
class AccessScopeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'parent_scope_id' => null,
            'scope_type' => ScopeType::Tenant,
            'company_id' => null,
            'campus_id' => null,
            'institute_id' => null,
            'name' => 'Tenant Scope',
            'code' => strtoupper(fake()->unique()->bothify('TENANT-####')),
            'status' => IdentityStatus::Active,
            'metadata' => [],
        ];
    }

    public function forCompany(Company $company, AccessScope $parent): static
    {
        return $this->state(fn () => [
            'tenant_id' => $company->tenant_id,
            'parent_scope_id' => $parent->id,
            'scope_type' => ScopeType::Company,
            'company_id' => $company->id,
            'campus_id' => null,
            'institute_id' => null,
            'name' => $company->name,
            'code' => 'COMPANY-'.$company->id,
        ]);
    }

    public function forCampus(Campus $campus, AccessScope $parent): static
    {
        return $this->state(fn () => [
            'tenant_id' => $campus->tenant_id,
            'parent_scope_id' => $parent->id,
            'scope_type' => ScopeType::Campus,
            'company_id' => $campus->company_id,
            'campus_id' => $campus->id,
            'institute_id' => null,
            'name' => $campus->name,
            'code' => 'CAMPUS-'.$campus->id,
        ]);
    }

    public function forInstitute(Institute $institute, AccessScope $parent): static
    {
        return $this->state(fn () => [
            'tenant_id' => $institute->tenant_id,
            'parent_scope_id' => $parent->id,
            'scope_type' => ScopeType::Institute,
            'company_id' => $institute->company_id,
            'campus_id' => $institute->campus_id,
            'institute_id' => $institute->id,
            'name' => $institute->name,
            'code' => 'INSTITUTE-'.$institute->id,
        ]);
    }
}
