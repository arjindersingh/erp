<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Identity\UserMembership;
use App\Models\User;
use App\Shared\Support\BelongsToTenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\RoleAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property RoleAssignmentStatus $status
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property int $tenant_id
 * @property int $user_id
 * @property int $user_membership_id
 * @property int $access_scope_id
 */
#[UseFactory(RoleAssignmentFactory::class)]
#[Fillable([
    'uuid',
    'tenant_id',
    'user_id',
    'user_membership_id',
    'role_id',
    'access_scope_id',
    'is_primary',
    'status',
    'starts_at',
    'ends_at',
    'assigned_by',
    'approved_by',
    'approved_at',
])]
class RoleAssignment extends Model
{
    /** @use HasFactory<RoleAssignmentFactory> */
    use BelongsToTenant, HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'status' => RoleAssignmentStatus::Active->value,
        'is_primary' => false,
    ];

    protected static function booted(): void
    {
        static::saving(
            fn (RoleAssignment $assignment) => app(RoleAssignmentValidator::class)->validate($assignment)
        );

        static::deleting(function (RoleAssignment $assignment): void {
            if (! $assignment->isForceDeleting()) {
                DB::table($assignment->getTable())
                    ->where($assignment->getKeyName(), $assignment->getKey())
                    ->update(['active_identity_key' => null]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => RoleAssignmentStatus::class,
            'is_primary' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<UserMembership, $this> */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(UserMembership::class, 'user_membership_id');
    }

    /** @return BelongsTo<Role, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** @return BelongsTo<AccessScope, $this> */
    public function accessScope(): BelongsTo
    {
        return $this->belongsTo(AccessScope::class);
    }

    /** @return BelongsTo<User, $this> */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** @return BelongsTo<User, $this> */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @param  Builder<RoleAssignment>  $query
     * @return Builder<RoleAssignment>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', RoleAssignmentStatus::Active->value);
    }

    /**
     * @param  Builder<RoleAssignment>  $query
     * @return Builder<RoleAssignment>
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

    public function isActiveAt(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status->isActive()
            && ! $this->trashed()
            && ($this->starts_at === null || $this->starts_at->lessThanOrEqualTo($at))
            && ($this->ends_at === null || $this->ends_at->greaterThan($at));
    }
}
