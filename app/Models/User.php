<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Authorization\RoleAssignment;
use App\Core\Identity\AccountStatus;
use App\Core\Identity\AccountType;
use App\Core\Identity\MembershipStatus;
use App\Core\Identity\Person;
use App\Core\Identity\Profile;
use App\Core\Identity\UserMembership;
use App\Core\Identity\UserPersonLink;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property AccountStatus $status
 * @property AccountType $account_type
 * @property Carbon|null $locked_until
 * @property Carbon|null $password_expires_at
 */
#[Fillable([
    'uuid',
    'name',
    'account_type',
    'username',
    'email',
    'mobile',
    'password',
    'status',
    'email_verified_at',
    'mobile_verified_at',
    'last_login_at',
    'last_login_ip',
    'failed_login_attempts',
    'locked_until',
    'must_change_password',
    'password_changed_at',
    'password_expires_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasPublicUuid, HasRoles, Notifiable, SoftDeletes;

    protected $attributes = [
        'account_type' => AccountType::Person->value,
        'status' => AccountStatus::Pending->value,
        'failed_login_attempts' => 0,
        'must_change_password' => false,
    ];

    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
            'status' => AccountStatus::class,
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',
            'password_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** @return HasMany<UserPersonLink, $this> */
    public function personLinks(): HasMany
    {
        return $this->hasMany(UserPersonLink::class);
    }

    /** @return BelongsToMany<Person, $this> */
    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'user_person_links')
            ->withPivot(['tenant_id', 'is_primary', 'status'])
            ->withTimestamps();
    }

    /** @return HasManyThrough<Profile, UserPersonLink, $this> */
    public function profiles(): HasManyThrough
    {
        return $this->hasManyThrough(
            Profile::class,
            UserPersonLink::class,
            'user_id',
            'person_id',
            'id',
            'person_id',
        );
    }

    /** @return HasMany<UserMembership, $this> */
    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    /** @return HasMany<UserMembership, $this> */
    public function activeMemberships(): HasMany
    {
        $now = now();

        return $this->hasMany(UserMembership::class)
            ->where('status', MembershipStatus::Active->value)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', $now));
    }

    /** @return HasMany<RoleAssignment, $this> */
    public function roleAssignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }

    public function isActive(): bool
    {
        return $this->status->allowsAuthentication();
    }

    public function isTemporarilyLocked(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status === AccountStatus::Locked
            || ($this->locked_until !== null && $this->locked_until->greaterThan($at));
    }

    public function isPasswordExpired(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->password_expires_at !== null
            && $this->password_expires_at->lessThanOrEqualTo($at);
    }
}
