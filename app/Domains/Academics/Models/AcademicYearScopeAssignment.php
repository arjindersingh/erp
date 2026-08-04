<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Core\Authorization\AccessScope;
use App\Domains\Academics\Enums\AcademicYearAssignmentStatus;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

final class AcademicYearScopeAssignment extends Model
{
    use BelongsToTenant, HasPublicUuid;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::saving(function (self $assignment): void {
            $year = AcademicYear::withoutGlobalScopes()->find($assignment->academic_year_id);
            $scope = AccessScope::withoutGlobalScopes()->find($assignment->access_scope_id);
            if (! $year || ! $scope || (int) $year->tenant_id !== (int) $assignment->tenant_id || (int) $scope->tenant_id !== (int) $assignment->tenant_id || ! $year->containsScope($scope)) {
                throw ValidationException::withMessages(['access_scope_id' => 'The academic year and scope must belong to the same tenant and boundary.']);
            }
            if ($assignment->starts_at && $assignment->ends_at && ! $assignment->starts_at->lt($assignment->ends_at)) {
                throw ValidationException::withMessages(['ends_at' => 'Assignment end must be after its start.']);
            }
        });
    }

    protected function casts(): array
    {
        return ['status' => AcademicYearAssignmentStatus::class, 'is_default' => 'boolean', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];
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
        return $this->status === AcademicYearAssignmentStatus::Active
            && ($this->starts_at === null || $this->starts_at->lte(now()))
            && ($this->ends_at === null || $this->ends_at->gt(now()));
    }
}
