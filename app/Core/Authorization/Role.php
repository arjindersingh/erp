<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;
use Spatie\Permission\Models\Role as SpatieRole;

#[UseFactory(RoleFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'name', 'code', 'guard_name', 'description', 'role_type', 'is_system', 'is_editable', 'is_assignable', 'status', 'created_by', 'updated_by'])]
class Role extends SpatieRole
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'guard_name' => 'web',
        'role_type' => RoleType::Staff->value,
        'is_system' => false,
        'is_editable' => true,
        'is_assignable' => true,
        'status' => AccessStatus::Active->value,
    ];

    protected static function booted(): void
    {
        static::deleting(function (Role $role): void {
            if ($role->is_system || ! $role->is_editable) {
                throw new LogicException('Protected system roles cannot be deleted.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'role_type' => RoleType::class,
            'is_system' => 'boolean',
            'is_editable' => 'boolean',
            'is_assignable' => 'boolean',
            'status' => AccessStatus::class,
        ];
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** @return BelongsToMany<Permission, $this> */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot(['tenant_id', 'granted_by', 'granted_at', 'expires_at', 'status'])
            ->withTimestamps();
    }

    /** @return HasMany<RoleAssignment, $this> */
    public function assignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }

    public function isAssignable(): bool
    {
        return $this->is_assignable && $this->status === AccessStatus::Active && ! $this->trashed();
    }

    public function grantPermission(Permission $permission, ?User $grantor = null): void
    {
        if ($this->guard_name !== $permission->guard_name) {
            throw new LogicException('Role and permission guards must match.');
        }

        $this->permissions()->syncWithoutDetaching([$permission->getKey() => [
            'tenant_id' => $this->tenant_id,
            'granted_by' => $grantor?->getKey(),
            'granted_at' => now(),
            'status' => RolePermissionStatus::Active->value,
        ]]);
    }
}
