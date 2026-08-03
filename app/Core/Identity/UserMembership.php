<?php

declare(strict_types=1);

namespace App\Core\Identity;

use App\Core\Authorization\AccessScope;
use App\Core\Authorization\RoleAssignment;
use App\Models\User;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\UserMembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property MembershipType $membership_type
 * @property MembershipStatus $status
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property int $tenant_id
 * @property int $user_id
 * @property int|null $person_id
 * @property int|null $profile_id
 * @property int $access_scope_id
 */
#[UseFactory(UserMembershipFactory::class)]
#[Fillable([
    'uuid',
    'tenant_id',
    'user_id',
    'person_id',
    'profile_id',
    'access_scope_id',
    'membership_type',
    'is_primary',
    'starts_at',
    'ends_at',
    'status',
    'created_by',
    'approved_by',
    'approved_at',
    'remarks',
    'metadata',
])]
class UserMembership extends Model
{
    /** @use HasFactory<UserMembershipFactory> */
    use BelongsToTenant, HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'status' => MembershipStatus::Pending->value,
        'is_primary' => false,
    ];

    protected static function booted(): void
    {
        static::saving(
            fn (UserMembership $membership) => app(MembershipValidator::class)->prepareAndValidate($membership)
        );

        static::deleting(function (UserMembership $membership): void {
            if (! $membership->isForceDeleting()) {
                DB::table($membership->getTable())
                    ->where($membership->getKeyName(), $membership->getKey())
                    ->update(['active_identity_key' => null]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'membership_type' => MembershipType::class,
            'is_primary' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => MembershipStatus::class,
            'approved_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Person, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /** @return BelongsTo<Profile, $this> */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /** @return BelongsTo<AccessScope, $this> */
    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** @return HasMany<RoleAssignment, $this> */
    public function roleAssignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }

    /**
     * @param  Builder<UserMembership>  $query
     * @return Builder<UserMembership>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', MembershipStatus::Active->value);
    }

    /**
     * @param  Builder<UserMembership>  $query
     * @return Builder<UserMembership>
     */
    public function scopeValidAt(Builder $query, ?Carbon $at = null): Builder
    {
        $at ??= now();

        return $query
            ->where(fn (Builder $query) => $query
                ->whereNull('starts_at')
                ->orWhere('starts_at', '<=', $at))
            ->where(fn (Builder $query) => $query
                ->whereNull('ends_at')
                ->orWhere('ends_at', '>', $at));
    }

    /**
     * @param  Builder<UserMembership>  $query
     * @return Builder<UserMembership>
     */
    public function scopeSelectable(Builder $query, ?Carbon $at = null): Builder
    {
        return $query
            ->active()
            ->validAt($at)
            ->whereHas('accessScope', fn (Builder $query) => $query
                ->where('status', IdentityStatus::Active->value)
                ->whereNull('deleted_at'));
    }

    /**
     * @param  Builder<UserMembership>  $query
     * @return Builder<UserMembership>
     */
    public function scopeForUser(Builder $query, User|int $user): Builder
    {
        return $query->where('user_id', $user instanceof User ? $user->getKey() : $user);
    }

    /**
     * @param  Builder<UserMembership>  $query
     * @return Builder<UserMembership>
     */
    public function scopeForScope(Builder $query, AccessScope|int $scope): Builder
    {
        return $query->where('access_scope_id', $scope instanceof AccessScope ? $scope->getKey() : $scope);
    }

    public function isActiveAt(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status->isActive()
            && ! $this->trashed()
            && ($this->starts_at === null || $this->starts_at->lessThanOrEqualTo($at))
            && ($this->ends_at === null || $this->ends_at->greaterThan($at));
    }

    public function isSelectableAt(?Carbon $at = null): bool
    {
        return $this->isActiveAt($at)
            && $this->accessScope()->first()?->isActive() === true;
    }
}
