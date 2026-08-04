<?php

declare(strict_types=1);

namespace App\Domains\Academics\Services;

use App\Core\Authorization\AccessScope;
use App\Core\Organization\Institute;
use App\Domains\Academics\Models\AcademicYear;

final class AcademicYearResolver
{
    public function currentForInstitute(Institute $institute): ?AcademicYear
    {
        return AcademicYear::withoutGlobalScopes()->where('tenant_id', $institute->tenant_id)
            ->where('is_current', true)->where('is_default', true)
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $institute->company_id))
            ->where(fn ($query) => $query->whereNull('campus_id')->orWhere('campus_id', $institute->campus_id))
            ->where(fn ($query) => $query->whereNull('institute_id')->orWhere('institute_id', $institute->id))
            ->orderByRaw('CASE WHEN institute_id IS NULL THEN 0 ELSE 4 END + CASE WHEN campus_id IS NULL THEN 0 ELSE 2 END + CASE WHEN company_id IS NULL THEN 0 ELSE 1 END DESC')
            ->first();
    }

    public function currentForScope(AccessScope $scope): ?AcademicYear
    {
        return AcademicYear::withoutGlobalScopes()->where('tenant_id', $scope->tenant_id)
            ->where('is_current', true)->where('is_default', true)
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $scope->company_id))
            ->where(fn ($query) => $query->whereNull('campus_id')->orWhere('campus_id', $scope->campus_id))
            ->where(fn ($query) => $query->whereNull('institute_id')->orWhere('institute_id', $scope->institute_id))
            ->orderByRaw('CASE WHEN institute_id IS NULL THEN 0 ELSE 4 END + CASE WHEN campus_id IS NULL THEN 0 ELSE 2 END + CASE WHEN company_id IS NULL THEN 0 ELSE 1 END DESC')
            ->first();
    }
}
