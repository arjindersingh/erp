<?php

declare(strict_types=1);

namespace App\Core\AcademicYear;

use App\Core\Authorization\AccessScope;
use App\Domains\Academics\Models\AcademicYear;

final class AcademicYearLockService
{
    public function isWritable(AcademicYear $year, AccessScope $scope, ?string $moduleKey = null, ?string $resourceType = null): bool
    {
        if ($year->isReadOnly()) {
            return false;
        }

        return ! $year->locks()->with('accessScope')->get()->contains(function ($lock) use ($scope, $moduleKey, $resourceType): bool {
            if (! $lock->isEffective()) {
                return false;
            }
            if ($lock->accessScope && ! $lock->accessScope->containsScope($scope)) {
                return false;
            }
            if ($lock->module_key !== null && $lock->module_key !== $moduleKey) {
                return false;
            }

            return $lock->resource_type === null || $lock->resource_type === $resourceType;
        });
    }
}
