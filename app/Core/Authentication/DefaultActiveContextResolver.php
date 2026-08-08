<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use App\Core\Authorization\AccessScope;
use App\Core\Identity\MembershipType;
use App\Core\Identity\UserMembership;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Services\AcademicYearResolver;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final readonly class DefaultActiveContextResolver
{
    public function __construct(
        private ActiveContextService $contexts,
        private AcademicYearResolver $academicYears,
    ) {}

    public function resolve(User $user, Tenant $tenant): ?ActiveContext
    {
        $memberships = UserMembership::withoutGlobalScopes()
            ->with('accessScope')
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->selectable()
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        foreach ($memberships as $membership) {
            $scope = $membership->accessScope;
            $portalCode = $this->portalFor($membership);

            if (! $scope instanceof AccessScope || $portalCode === null) {
                continue;
            }

            $year = $this->academicYears->currentForScope($scope);

            if ($year === null) {
                continue;
            }

            try {
                return $this->contexts->select($user, $tenant, $membership->uuid, $portalCode, $year->uuid);
            } catch (ValidationException) {
                // Try the next membership when its stored defaults are no longer valid.
            }
        }

        return null;
    }

    private function portalFor(UserMembership $membership): ?string
    {
        $configuredCodes = $membership->metadata['portal_codes'] ?? [];
        $portalCodes = is_array($configuredCodes)
            ? array_values(array_unique(array_filter($configuredCodes, 'is_string')))
            : [];
        $preferredCode = $this->preferredPortalFor($membership->membership_type);

        if ($portalCodes === []) {
            $portalCodes[] = $preferredCode ?? 'administration';
        }

        $activeCodes = Portal::query()
            ->where('status', 'active')
            ->whereIn('code', $portalCodes)
            ->pluck('code')
            ->all();

        if ($preferredCode !== null && in_array($preferredCode, $activeCodes, true)) {
            return $preferredCode;
        }

        foreach ($portalCodes as $portalCode) {
            if (in_array($portalCode, $activeCodes, true)) {
                return $portalCode;
            }
        }

        return null;
    }

    private function preferredPortalFor(MembershipType $type): ?string
    {
        return match ($type) {
            MembershipType::SiteAdministration => 'site_admin',
            MembershipType::TenantAdministration => 'administration',
            MembershipType::Management => 'management',
            MembershipType::Employee => 'staff',
            MembershipType::Teacher => 'teacher',
            MembershipType::Student => 'student',
            MembershipType::Guardian => 'parent',
            MembershipType::Alumni => 'alumni',
            MembershipType::Service, MembershipType::External => null,
        };
    }
}
