<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Models\User;
use Illuminate\Support\Collection;

class ScopeAccessService
{
    public function canAccessScope(User $user, AccessScope $scope): bool
    {
        return $this->userScopes($user, $scope->tenant_id)
            ->contains(fn (AccessScope $assignedScope) => $assignedScope->containsScope($scope));
    }

    public function canAccessCompany(User $user, Company $company): bool
    {
        return $this->userScopes($user, (int) $company->tenant_id)
            ->contains(fn (AccessScope $scope) => $scope->canAccessCompany($company));
    }

    public function canAccessCampus(User $user, Campus $campus): bool
    {
        return $this->userScopes($user, (int) $campus->tenant_id)
            ->contains(fn (AccessScope $scope) => $scope->canAccessCampus($campus));
    }

    public function canAccessInstitute(User $user, Institute $institute): bool
    {
        return $this->userScopes($user, (int) $institute->tenant_id)
            ->contains(fn (AccessScope $scope) => $scope->canAccessInstitute($institute));
    }

    public function scopeContains(AccessScope $parent, AccessScope $child): bool
    {
        return $parent->containsScope($child);
    }

    /** @return Collection<int, AccessScope> */
    private function userScopes(User $user, int $tenantId): Collection
    {
        return $user->memberships()
            ->forTenant($tenantId)
            ->selectable()
            ->with('accessScope')
            ->get()
            ->map(fn ($membership) => $membership->accessScope)
            ->filter(fn ($scope): bool => $scope instanceof AccessScope && $scope->isActive())
            ->values();
    }
}
