<?php

declare(strict_types=1);

namespace App\Core\Identity;

use App\Models\User;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/** @property IdentityStatus $status */
#[UseFactory(PersonFactory::class)]
#[Fillable([
    'tenant_id',
    'uuid',
    'title',
    'first_name',
    'middle_name',
    'last_name',
    'display_name',
    'gender',
    'date_of_birth',
    'photo_path',
    'primary_email',
    'primary_mobile',
    'status',
    'metadata',
])]
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use BelongsToTenant, HasFactory, HasPublicUuid, SoftDeletes;

    protected $table = 'persons';

    protected $attributes = [
        'status' => IdentityStatus::Active->value,
    ];

    protected static function booted(): void
    {
        static::saving(function (Person $person): void {
            if (blank($person->getAttribute('display_name'))) {
                $person->setAttribute('display_name', $person->fullName());
            }
        });
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'status' => IdentityStatus::class,
            'metadata' => 'array',
        ];
    }

    public function fullName(): string
    {
        return collect([
            $this->getAttribute('first_name'),
            $this->getAttribute('middle_name'),
            $this->getAttribute('last_name'),
        ])->filter()->implode(' ');
    }

    /** @return HasMany<PersonContact, $this> */
    public function contacts(): HasMany
    {
        return $this->hasMany(PersonContact::class);
    }

    /** @return HasMany<UserPersonLink, $this> */
    public function userLinks(): HasMany
    {
        return $this->hasMany(UserPersonLink::class);
    }

    /** @return BelongsToMany<User, $this> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_person_links')
            ->withPivot(['tenant_id', 'is_primary', 'status'])
            ->withTimestamps();
    }

    /** @return HasMany<Profile, $this> */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /** @return HasMany<UserMembership, $this> */
    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    /** @return HasOne<Profile, $this> */
    public function employeeProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('type', ProfileType::Employee->value);
    }

    /** @return HasOne<Profile, $this> */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('type', ProfileType::Teacher->value);
    }

    /** @return HasOne<Profile, $this> */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('type', ProfileType::Student->value);
    }

    /** @return HasOne<Profile, $this> */
    public function guardianProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('type', ProfileType::Guardian->value);
    }

    /** @return HasOne<Profile, $this> */
    public function alumniProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('type', ProfileType::Alumni->value);
    }

    /** @return HasOne<Profile, $this> */
    public function managementProfile(): HasOne
    {
        return $this->hasOne(Profile::class)->where('type', ProfileType::Management->value);
    }
}
