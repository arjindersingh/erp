<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Core\Authorization\AccessScope;
use App\Domains\Academics\Enums\AcademicYearLockStatus;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

final class AcademicYearLock extends Model
{
    use BelongsToTenant, HasPublicUuid;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::saving(function (self $lock): void {
            $year = AcademicYear::withoutGlobalScopes()->find($lock->academic_year_id);
            $scope = $lock->access_scope_id ? AccessScope::withoutGlobalScopes()->find($lock->access_scope_id) : null;
            if (! $year || (int) $year->tenant_id !== (int) $lock->tenant_id || ($scope && ((int) $scope->tenant_id !== (int) $lock->tenant_id || ! $year->containsScope($scope)))) {
                throw ValidationException::withMessages(['academic_year_id' => 'Lock boundary must match the academic year tenant and scope.']);
            }
            if (blank($lock->reason)) {
                throw ValidationException::withMessages(['reason' => 'A lock reason is required.']);
            }
        });
    }

    protected function casts(): array
    {
        return ['status' => AcademicYearLockStatus::class, 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'released_at' => 'datetime'];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }

    public function isEffective(): bool
    {
        return $this->status === AcademicYearLockStatus::Active && $this->released_at === null
            && ($this->starts_at === null || $this->starts_at->lte(now()))
            && ($this->ends_at === null || $this->ends_at->gt(now()));
    }
}
