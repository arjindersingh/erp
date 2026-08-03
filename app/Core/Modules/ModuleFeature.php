<?php

declare(strict_types=1);

namespace App\Core\Modules;

use App\Core\Authorization\AccessStatus;
use App\Core\Authorization\Permission;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\ModuleFeatureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

#[UseFactory(ModuleFeatureFactory::class)]
#[Fillable(['module_id', 'feature_group_id', 'uuid', 'code', 'name', 'short_name', 'description', 'route_name', 'icon', 'display_order', 'feature_type', 'supports_search', 'supports_favourites', 'supports_quick_action', 'status'])]
class ModuleFeature extends Model
{
    /** @use HasFactory<ModuleFeatureFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = ['display_order' => 0, 'feature_type' => FeatureType::Resource->value, 'status' => AccessStatus::Active->value];

    protected static function booted(): void
    {
        static::saving(function (ModuleFeature $feature): void {
            if ($feature->feature_group_id !== null
                && ! ModuleFeatureGroup::query()->whereKey($feature->feature_group_id)->where('module_id', $feature->module_id)->exists()) {
                throw new LogicException('Feature group must belong to the same module.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'feature_type' => FeatureType::class,
            'supports_search' => 'boolean',
            'supports_favourites' => 'boolean',
            'supports_quick_action' => 'boolean',
            'status' => AccessStatus::class,
        ];
    }

    /** @return BelongsTo<Module, $this> */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /** @return BelongsTo<ModuleFeatureGroup, $this> */
    public function featureGroup(): BelongsTo
    {
        return $this->belongsTo(ModuleFeatureGroup::class, 'feature_group_id');
    }

    /** @return HasMany<Permission, $this> */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
