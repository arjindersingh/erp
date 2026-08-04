<?php

declare(strict_types=1);

namespace App\Core\AcademicYear;

use App\Core\Authorization\AccessScope;
use App\Core\Tenancy\TenantContext;
use App\Domains\Academics\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

final class SelectAcademicYearAction
{
    public function __construct(private AcademicYearContext $context, private AcademicYearAccessService $access, private TenantContext $tenant) {}

    public function optionId(AcademicYear $year, AccessScope $scope): string
    {
        return Crypt::encryptString(json_encode(['tenant' => $year->tenant_id, 'year' => $year->uuid, 'scope' => $scope->uuid], JSON_THROW_ON_ERROR));
    }

    public function execute(User $user, string $optionId): AcademicYear
    {
        try {
            $data = json_decode(Crypt::decryptString($optionId), true, flags: JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            throw ValidationException::withMessages(['academic_year' => 'Invalid academic year selection.']);
        }

        if ((int) ($data['tenant'] ?? 0) !== (int) $this->tenant->id()) {
            throw ValidationException::withMessages(['academic_year' => 'Invalid academic year selection.']);
        }
        $year = AcademicYear::withoutGlobalScopes()->where('tenant_id', $data['tenant'])->where('uuid', $data['year'] ?? '')->first();
        $scope = AccessScope::withoutGlobalScopes()->where('tenant_id', $data['tenant'])->where('uuid', $data['scope'] ?? '')->first();
        if (! $year || ! $scope || ! $this->access->canSelect($user, $year, $scope)) {
            throw ValidationException::withMessages(['academic_year' => 'Academic year is not available for this user and scope.']);
        }
        $this->context->activate($year, $scope);

        return $year;
    }
}
