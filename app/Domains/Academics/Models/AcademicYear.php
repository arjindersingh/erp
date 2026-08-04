<?php

declare(strict_types=1);

namespace App\Domains\Academics\Models;

use App\Domains\Academics\Enums\AcademicYearStatus;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\AcademicYearFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

#[UseFactory(AcademicYearFactory::class)]
final class AcademicYear extends Model
{
    use BelongsToTenant, HasFactory, HasPublicUuid, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::saving(function (self $year): void {
            $year->boundary_key = implode(':', [$year->tenant_id, $year->company_id ?? 0, $year->campus_id ?? 0, $year->institute_id ?? 0]);
            if ($year->starts_on === null || $year->ends_on === null || ! $year->starts_on->lt($year->ends_on)) {
                throw ValidationException::withMessages(['ends_on' => 'Academic year end date must be after its start date.']);
            }
            $originalStatus = $year->getOriginal('status');
            $originalStatus = $originalStatus instanceof AcademicYearStatus ? $originalStatus : ($originalStatus === null ? null : AcademicYearStatus::from($originalStatus));
            if ($year->exists && $originalStatus?->isReadOnly()) {
                throw ValidationException::withMessages(['status' => 'Locked, closed, and archived academic years are read-only.']);
            }
            if ($year->is_current && $year->is_default) {
                $duplicate = self::withoutGlobalScopes()->where('tenant_id', $year->tenant_id)
                    ->where('company_id', $year->company_id)->where('campus_id', $year->campus_id)
                    ->where('institute_id', $year->institute_id)->where('is_current', true)
                    ->where('is_default', true)->when($year->exists, fn ($query) => $query->whereKeyNot($year->getKey()))->exists();
                if ($duplicate) {
                    throw ValidationException::withMessages(['is_default' => 'Only one current default academic year is allowed for this scope.']);
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'starts_on' => 'date', 'ends_on' => 'date', 'admission_starts_on' => 'date', 'admission_ends_on' => 'date',
            'status' => AcademicYearStatus::class, 'is_current' => 'boolean', 'is_default' => 'boolean',
            'locked_at' => 'datetime', 'closed_at' => 'datetime',
        ];
    }

    public function isReadOnly(): bool
    {
        return $this->status->isReadOnly();
    }
}
