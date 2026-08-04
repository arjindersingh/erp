<?php

declare(strict_types=1);

namespace App\Domains\Students\Models;

use App\Domains\Students\Enums\RelationshipStatus;
use Database\Factories\StudentGuardianRelationshipFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

#[UseFactory(StudentGuardianRelationshipFactory::class)]
final class StudentGuardianRelationship extends StudentDomainModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::saving(function (self $relationship): void {
            $student = StudentProfile::withoutGlobalScopes()->find($relationship->student_profile_id);
            $guardian = GuardianProfile::withoutGlobalScopes()->find($relationship->guardian_profile_id);
            $type = GuardianRelationshipType::withoutGlobalScopes()->find($relationship->guardian_relationship_type_id);
            $errors = [];
            if (! $student || (int) $student->tenant_id !== (int) $relationship->tenant_id) {
                $errors['student_profile_id'] = 'Student must belong to the relationship tenant.';
            }
            if (! $guardian || (int) $guardian->tenant_id !== (int) $relationship->tenant_id) {
                $errors['guardian_profile_id'] = 'Guardian must belong to the relationship tenant.';
            }
            if (! $type || ($type->tenant_id !== null && (int) $type->tenant_id !== (int) $relationship->tenant_id)) {
                $errors['guardian_relationship_type_id'] = 'Relationship type must be shared or tenant-owned.';
            }
            if ($relationship->starts_on && $relationship->ends_on && $relationship->ends_on < $relationship->starts_on) {
                $errors['ends_on'] = 'End date must be on or after start date.';
            }
            $activeStatuses = [RelationshipStatus::Draft->value, RelationshipStatus::PendingApproval->value, RelationshipStatus::Active->value, RelationshipStatus::Suspended->value];
            $duplicate = self::withoutGlobalScopes()->where('tenant_id', $relationship->tenant_id)->where('student_profile_id', $relationship->student_profile_id)->where('guardian_profile_id', $relationship->guardian_profile_id)->where('guardian_relationship_type_id', $relationship->guardian_relationship_type_id)->whereIn('status', $activeStatuses)->whereNot('id', $relationship->id ?? 0)->exists();
            if ($duplicate) {
                $errors['guardian_profile_id'] = 'An active relationship of this type already exists.';
            }
            if ($relationship->is_primary_guardian) {
                $primaryExists = self::withoutGlobalScopes()->where('tenant_id', $relationship->tenant_id)->where('student_profile_id', $relationship->student_profile_id)->where('is_primary_guardian', true)->where('status', RelationshipStatus::Active->value)->whereNot('id', $relationship->id ?? 0)->exists();
                if ($primaryExists) {
                    $errors['is_primary_guardian'] = 'Only one active primary guardian is allowed by the default policy.';
                }
            }
            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }
        });
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function guardianProfile(): BelongsTo
    {
        return $this->belongsTo(GuardianProfile::class);
    }

    public function relationshipType(): BelongsTo
    {
        return $this->belongsTo(GuardianRelationshipType::class, 'guardian_relationship_type_id');
    }

    public function isEffectiveOn(DateTimeInterface $date): bool
    {
        $day = Carbon::instance($date)->startOfDay();
        $startsValue = $this->getRawOriginal('starts_on');
        $endsValue = $this->getRawOriginal('ends_on');
        $starts = $startsValue === null ? null : Carbon::parse((string) $startsValue);
        $ends = $endsValue === null ? null : Carbon::parse((string) $endsValue);
        $status = RelationshipStatus::tryFrom((string) $this->getRawOriginal('status'));

        return $status === RelationshipStatus::Active && ($starts === null || $starts->lte($day)) && ($ends === null || $ends->endOfDay()->gte($day));
    }

    protected function casts(): array
    {
        return ['is_primary_guardian' => 'boolean', 'is_legal_guardian' => 'boolean', 'is_financial_guardian' => 'boolean', 'is_academic_contact' => 'boolean', 'is_emergency_contact' => 'boolean', 'is_pickup_authorised' => 'boolean', 'is_medical_consent_authority' => 'boolean', 'is_portal_contact' => 'boolean', 'is_residential_guardian' => 'boolean', 'starts_on' => 'date', 'ends_on' => 'date', 'communication_preference_json' => 'array', 'approved_at' => 'datetime', 'status' => RelationshipStatus::class];
    }
}
