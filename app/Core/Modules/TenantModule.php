<?php

declare(strict_types=1);

namespace App\Core\Modules;

use App\Models\User;
use App\Shared\Support\BelongsToTenant;
use Database\Factories\TenantModuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LogicException;

#[UseFactory(TenantModuleFactory::class)]
#[Fillable(['tenant_id', 'module_id', 'is_enabled', 'enabled_at', 'disabled_at', 'starts_at', 'ends_at', 'configuration_json', 'display_name_override', 'icon_override', 'display_order_override', 'created_by', 'updated_by'])]
class TenantModule extends Model
{
    /** @use HasFactory<TenantModuleFactory> */
    use BelongsToTenant, HasFactory;

    protected $attributes = ['is_enabled' => false];

    protected static function booted(): void
    {
        static::saving(function (TenantModule $configuration): void {
            if ($configuration->starts_at !== null && $configuration->ends_at !== null && $configuration->ends_at->lessThanOrEqualTo($configuration->starts_at)) {
                throw new LogicException('Module end date must be later than its start date.');
            }

            if (! $configuration->is_enabled && $configuration->module()->where('is_core', true)->exists()) {
                throw new LogicException('Core modules cannot be disabled.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'enabled_at' => 'datetime',
            'disabled_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'configuration_json' => 'array',
            'display_order_override' => 'integer',
        ];
    }

    /** @return BelongsTo<Module, $this> */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isEffectiveAt(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->is_enabled
            && ($this->starts_at === null || $this->starts_at->lessThanOrEqualTo($at))
            && ($this->ends_at === null || $this->ends_at->isAfter($at));
    }
}
