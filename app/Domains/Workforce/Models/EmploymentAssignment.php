<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Core\Authorization\AccessScope;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Domains\Workforce\Services\EmploymentAssignmentValidator;
use Database\Factories\EmploymentAssignmentFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[UseFactory(EmploymentAssignmentFactory::class)]
final class EmploymentAssignment extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::saving(fn (self $assignment) => app(EmploymentAssignmentValidator::class)->validate($assignment));
        self::created(function (self $assignment): void {
            if ($assignment->is_primary) {
                $assignment->employeeProfile()->update(['primary_employment_assignment_id' => $assignment->id]);
            }
            $assignment->histories()->create([
                'tenant_id' => $assignment->tenant_id, 'action' => 'appointment',
                'new_values_json' => $assignment->getAttributes(), 'effective_on' => $assignment->starts_on,
                'changed_by' => $assignment->created_by,
            ]);
        });
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }

    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }

    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reports_to_assignment_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(EmploymentAssignmentHistory::class);
    }

    public function isEffectiveOn(DateTimeInterface $date): bool
    {
        $day = Carbon::instance($date)->startOfDay();
        $startsOn = $this->getAttribute('starts_on');
        $endsOn = $this->getAttribute('ends_on');

        if (! $startsOn instanceof Carbon || ($endsOn !== null && ! $endsOn instanceof Carbon)) {
            return false;
        }

        return $this->status === 'active'
            && $startsOn->startOfDay()->lte($day)
            && ($endsOn === null || $endsOn->endOfDay()->gte($day));
    }

    protected function casts(): array
    {
        return ['appointment_date' => 'date', 'starts_on' => 'date', 'ends_on' => 'date', 'probation_ends_on' => 'date', 'approved_at' => 'datetime', 'is_primary' => 'boolean', 'is_additional_posting' => 'boolean', 'is_acting_assignment' => 'boolean', 'workload_percentage' => 'decimal:2'];
    }
}
