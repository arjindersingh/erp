<?php

declare(strict_types=1);

namespace App\Domains\Students\Models;

use App\Core\Identity\Person;
use App\Domains\Academics\Models\EducationLevel;
use App\Domains\Students\Enums\ProfileRecordStatus;
use Database\Factories\GuardianProfileFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

#[UseFactory(GuardianProfileFactory::class)]
final class GuardianProfile extends StudentDomainModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::saving(function (self $profile): void {
            $personTenant = Person::withoutGlobalScopes()->whereKey($profile->person_id)->value('tenant_id');
            if ((int) $personTenant !== (int) $profile->tenant_id) {
                throw ValidationException::withMessages(['person_id' => 'Person must belong to the guardian tenant.']);
            }
        });
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(GuardianOccupation::class, 'occupation_id');
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function studentRelationships(): HasMany
    {
        return $this->hasMany(StudentGuardianRelationship::class);
    }

    protected function casts(): array
    {
        return ['portal_access_allowed' => 'boolean', 'communication_allowed' => 'boolean', 'financial_contact_allowed' => 'boolean', 'verified_at' => 'datetime', 'status' => ProfileRecordStatus::class];
    }
}
