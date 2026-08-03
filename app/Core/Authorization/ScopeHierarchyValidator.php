<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Authorization\Exceptions\InvalidAccessScope;
use App\Core\Identity\IdentityStatus;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use Illuminate\Support\Str;

class ScopeHierarchyValidator
{
    public function prepareAndValidate(AccessScope $scope): void
    {
        $this->prepareIdentity($scope);
        $this->validateOrganizationPath($scope);
        $parent = $this->validateParent($scope);
        $this->preventUnsafeHierarchyChanges($scope);

        $scope->setAttribute('level', $scope->scope_type->level());
        $scope->setAttribute(
            'path',
            $parent === null
                ? '/'.$scope->uuid
                : rtrim($parent->path, '/').'/'.$scope->uuid,
        );
    }

    private function prepareIdentity(AccessScope $scope): void
    {
        if (blank($scope->getAttribute('uuid'))) {
            $scope->setAttribute('uuid', (string) Str::uuid());
        }

        $name = trim((string) $scope->getAttribute('name'));
        $code = Str::upper(trim((string) $scope->getAttribute('code')));

        if ($name === '' || $code === '') {
            throw InvalidAccessScope::because('name and code are required.');
        }

        $scope->setAttribute('name', $name);
        $scope->setAttribute('code', $code);
    }

    private function validateOrganizationPath(AccessScope $scope): void
    {
        $tenantId = (int) $scope->tenant_id;
        $companyId = $this->nullableId($scope->company_id);
        $campusId = $this->nullableId($scope->campus_id);
        $instituteId = $this->nullableId($scope->institute_id);

        match ($scope->scope_type) {
            ScopeType::Tenant => $this->requireIds($companyId, $campusId, $instituteId, false, false, false),
            ScopeType::Company => $this->validateCompany($tenantId, $companyId, $campusId, $instituteId),
            ScopeType::Campus => $this->validateCampus($tenantId, $companyId, $campusId, $instituteId),
            ScopeType::Institute => $this->validateInstitute($tenantId, $companyId, $campusId, $instituteId),
        };
    }

    private function validateCompany(int $tenantId, ?int $companyId, ?int $campusId, ?int $instituteId): void
    {
        $this->requireIds($companyId, $campusId, $instituteId, true, false, false);

        if (! Company::query()->where('tenant_id', $tenantId)->whereKey($companyId)->exists()) {
            throw InvalidAccessScope::because('company does not belong to the tenant.');
        }
    }

    private function validateCampus(int $tenantId, ?int $companyId, ?int $campusId, ?int $instituteId): void
    {
        $this->requireIds($companyId, $campusId, $instituteId, true, true, false);

        if (! Campus::query()
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->whereKey($campusId)
            ->exists()) {
            throw InvalidAccessScope::because('campus does not belong to the tenant and company.');
        }
    }

    private function validateInstitute(int $tenantId, ?int $companyId, ?int $campusId, ?int $instituteId): void
    {
        $this->requireIds($companyId, $campusId, $instituteId, true, true, true);

        if (! Institute::query()
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->where('campus_id', $campusId)
            ->whereKey($instituteId)
            ->exists()) {
            throw InvalidAccessScope::because('institute does not belong to the tenant, company, and campus.');
        }
    }

    private function validateParent(AccessScope $scope): ?AccessScope
    {
        $expectedType = $scope->scope_type->parentType();
        $parentId = $this->nullableId($scope->parent_scope_id);

        if ($expectedType === null) {
            if ($parentId !== null) {
                throw InvalidAccessScope::because('tenant scope cannot have a parent.');
            }

            return null;
        }

        if ($parentId === null) {
            throw InvalidAccessScope::because($scope->scope_type->value.' scope requires a parent.');
        }

        $parent = AccessScope::query()
            ->where('tenant_id', $scope->tenant_id)
            ->whereKey($parentId)
            ->first();

        if ($parent === null || $parent->scope_type !== $expectedType) {
            throw InvalidAccessScope::because('parent must be an active '.$expectedType->value.' scope in the same tenant.');
        }

        if ($parent->status !== IdentityStatus::Active) {
            throw InvalidAccessScope::because('parent scope is not active.');
        }

        if ($scope->scope_type === ScopeType::Campus && $parent->company_id !== $scope->company_id) {
            throw InvalidAccessScope::because('campus scope must inherit its company from the parent scope.');
        }

        if ($scope->scope_type === ScopeType::Institute
            && ($parent->company_id !== $scope->company_id || $parent->campus_id !== $scope->campus_id)) {
            throw InvalidAccessScope::because('institute scope must inherit its company and campus from the parent scope.');
        }

        return $parent;
    }

    private function preventUnsafeHierarchyChanges(AccessScope $scope): void
    {
        if (! $scope->exists) {
            return;
        }

        if ($scope->isDirty(['uuid', 'tenant_id'])) {
            throw InvalidAccessScope::because('scope UUID and tenant cannot be changed.');
        }

        if (! $scope->isDirty([
            'parent_scope_id',
            'scope_type',
            'company_id',
            'campus_id',
            'institute_id',
        ])) {
            return;
        }

        if ($scope->children()->exists() || $scope->memberships()->exists()) {
            throw InvalidAccessScope::because('a scope with children or memberships cannot be re-parented.');
        }
    }

    private function requireIds(
        ?int $companyId,
        ?int $campusId,
        ?int $instituteId,
        bool $companyRequired,
        bool $campusRequired,
        bool $instituteRequired,
    ): void {
        $valid = ($companyRequired ? $companyId !== null : $companyId === null)
            && ($campusRequired ? $campusId !== null : $campusId === null)
            && ($instituteRequired ? $instituteId !== null : $instituteId === null);

        if (! $valid) {
            throw InvalidAccessScope::because('organization identifiers do not match the scope type.');
        }
    }

    private function nullableId(mixed $value): ?int
    {
        return $value === null ? null : (int) $value;
    }
}
