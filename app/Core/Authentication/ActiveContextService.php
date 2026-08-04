<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use App\Core\Identity\UserMembership;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class ActiveContextService
{
    public function select(User $user, Tenant $tenant, string $membershipUuid, string $portalCode, string $yearUuid): ActiveContext
    {
        $membership = UserMembership::withoutGlobalScopes()->with('accessScope')->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)->where('uuid', $membershipUuid)->selectable()->first();
        if ($membership === null) {
            throw ValidationException::withMessages(['membership' => 'The selected membership is not available.']);
        }
        $allowedPortals = $membership->metadata['portal_codes'] ?? ['administration'];
        $portal = Portal::query()->where('code', $portalCode)->where('status', 'active')->first();
        if ($portal === null || ! in_array($portalCode, $allowedPortals, true)) {
            throw ValidationException::withMessages(['portal' => 'The selected portal is not available.']);
        }
        $year = AcademicYear::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('uuid', $yearUuid)
            ->whereIn('status', ['active', 'locked'])->first();
        if ($year === null || ! $year->containsScope($membership->accessScope)) {
            throw ValidationException::withMessages(['academic_year' => 'The selected academic year is not available for this membership.']);
        }

        return new ActiveContext($membership, $membership->accessScope, $portal, $year);
    }
}
