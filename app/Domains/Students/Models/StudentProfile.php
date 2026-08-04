<?php

declare(strict_types=1);

namespace App\Domains\Students\Models;

use App\Core\Identity\Person;
use App\Domains\Students\Enums\ProfileRecordStatus;
use App\Domains\Students\Enums\StudentType;
use Database\Factories\StudentProfileFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

#[UseFactory(StudentProfileFactory::class)]
final class StudentProfile extends StudentDomainModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::saving(function (self $profile): void {
            $person = Person::withoutGlobalScopes()->find($profile->person_id);
            $studentStatus = StudentStatus::withoutGlobalScopes()->find($profile->student_status_id);
            $category = $profile->student_category_id ? StudentCategory::withoutGlobalScopes()->find($profile->student_category_id) : null;
            $errors = [];
            if (! $person || (int) $person->tenant_id !== (int) $profile->tenant_id) {
                $errors['person_id'] = 'Person must belong to the student tenant.';
            }
            if (! $studentStatus || ($studentStatus->tenant_id !== null && (int) $studentStatus->tenant_id !== (int) $profile->tenant_id)) {
                $errors['student_status_id'] = 'Student status must be shared or owned by the student tenant.';
            }
            if ($profile->student_category_id && (! $category || ($category->tenant_id !== null && (int) $category->tenant_id !== (int) $profile->tenant_id))) {
                $errors['student_category_id'] = 'Student category must be shared or owned by the student tenant.';
            }
            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }
        });
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function studentCategory(): BelongsTo
    {
        return $this->belongsTo(StudentCategory::class);
    }

    public function studentStatus(): BelongsTo
    {
        return $this->belongsTo(StudentStatus::class);
    }

    public function guardianRelationships(): HasMany
    {
        return $this->hasMany(StudentGuardianRelationship::class);
    }

    protected function casts(): array
    {
        return ['registration_date' => 'date', 'first_admission_date' => 'date', 'portal_access_allowed' => 'boolean', 'communication_allowed' => 'boolean', 'verified_at' => 'datetime', 'student_type' => StudentType::class, 'status' => ProfileRecordStatus::class];
    }
}
