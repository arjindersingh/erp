<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Tenancy\Tenant;
use App\Models\User;
use Database\Factories\RolePermissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[UseFactory(RolePermissionFactory::class)]
#[Fillable(['tenant_id', 'role_id', 'permission_id', 'granted_by', 'granted_at', 'expires_at', 'status'])]
class RolePermission extends Model
{
    /** @use HasFactory<RolePermissionFactory> */
    use HasFactory;

    protected $attributes = ['status' => RolePermissionStatus::Active->value];

    protected function casts(): array
    {
        return [
            'granted_at' => 'datetime',
            'expires_at' => 'datetime',
            'status' => RolePermissionStatus::class,
        ];
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** @return BelongsTo<Role, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** @return BelongsTo<Permission, $this> */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /** @return BelongsTo<User, $this> */
    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function isEffectiveAt(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status === RolePermissionStatus::Active
            && ($this->expires_at === null || $this->expires_at->isAfter($at));
    }
}
