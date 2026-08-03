<?php

declare(strict_types=1);

namespace App\Core\Authorization;

use App\Core\Authorization\Exceptions\InvalidPermissionCode;
use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeature;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[UseFactory(PermissionFactory::class)]
#[Fillable(['uuid', 'module_id', 'module_feature_id', 'name', 'code', 'guard_name', 'command', 'description', 'permission_type', 'is_system', 'status'])]
class Permission extends SpatiePermission
{
    /** @use HasFactory<PermissionFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'guard_name' => 'web',
        'permission_type' => PermissionType::Command->value,
        'is_system' => true,
        'status' => AccessStatus::Active->value,
    ];

    protected static function booted(): void
    {
        static::saving(function (Permission $permission): void {
            $permission->code = $permission->code ?: $permission->name;
            $permission->name = $permission->code;

            if (! preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*){2}$/', $permission->code)
                && ! preg_match('/^[a-z][a-z0-9_]*\.access$/', $permission->code)) {
                throw InvalidPermissionCode::for($permission->code);
            }

            $module = Module::query()->find($permission->module_id);

            if ($module !== null && ! str_starts_with($permission->code, $module->code.'.')) {
                throw InvalidPermissionCode::for($permission->code);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'permission_type' => PermissionType::class,
            'is_system' => 'boolean',
            'status' => AccessStatus::class,
        ];
    }

    /** @return BelongsTo<Module, $this> */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /** @return BelongsTo<ModuleFeature, $this> */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(ModuleFeature::class, 'module_feature_id');
    }

    /** @return BelongsToMany<Role, $this> */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot(['tenant_id', 'granted_by', 'granted_at', 'expires_at', 'status'])
            ->withTimestamps();
    }
}
