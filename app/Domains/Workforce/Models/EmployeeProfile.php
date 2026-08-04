<?php

declare(strict_types=1);

namespace App\Domains\Workforce\Models;

use App\Core\Identity\Person;
use Database\Factories\EmployeeProfileFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

#[UseFactory(EmployeeProfileFactory::class)]
final class EmployeeProfile extends WorkforceModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::saving(function (self $profile): void {
            $personTenant = Person::withoutGlobalScopes()->whereKey($profile->person_id)->value('tenant_id');
            if ((int) $personTenant !== (int) $profile->tenant_id) {
                throw ValidationException::withMessages(['person_id' => 'The person must belong to the employee tenant.']);
            }
        });
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmploymentAssignment::class);
    }

    public function primaryAssignment(): BelongsTo
    {
        return $this->belongsTo(EmploymentAssignment::class, 'primary_employment_assignment_id');
    }

    protected function casts(): array
    {
        return ['joining_date' => 'date', 'confirmation_date' => 'date', 'retirement_date' => 'date', 'service_start_date' => 'date', 'service_end_date' => 'date'];
    }
}
