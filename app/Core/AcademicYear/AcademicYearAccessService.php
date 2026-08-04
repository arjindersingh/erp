<?php

declare(strict_types=1);

namespace App\Core\AcademicYear;

use App\Core\Authorization\AccessScope;
use App\Core\Authorization\ScopeAccessService;
use App\Domains\Academics\Enums\AcademicYearAssignmentStatus;
use App\Domains\Academics\Models\AcademicYear;
use App\Models\User;

final class AcademicYearAccessService
{
    public function __construct(private ScopeAccessService $scopes) {}

    public function canSelect(User $user, AcademicYear $year, AccessScope $scope): bool
    {
        if (! $year->isSelectable() || ! $year->containsScope($scope) || ! $this->scopes->canAccessScope($user, $scope)) {
            return false;
        }

        $assignments = $year->scopeAssignments()->where('status', AcademicYearAssignmentStatus::Active->value)->get();

        return $assignments->isEmpty() || $assignments->contains(fn ($assignment) => $assignment->isEffective() && $assignment->accessScope?->containsScope($scope));
    }
}
