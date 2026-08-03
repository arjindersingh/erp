<?php

declare(strict_types=1);

namespace App\Core\Modules;

use App\Core\Authorization\AccessStatus;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\ModuleFeatureGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(ModuleFeatureGroupFactory::class)]
#[Fillable(['module_id', 'uuid', 'code', 'name', 'description', 'icon', 'display_order', 'status'])]
class ModuleFeatureGroup extends Model
{
    /** @use HasFactory<ModuleFeatureGroupFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = ['display_order' => 0, 'status' => AccessStatus::Active->value];

    protected function casts(): array
    {
        return ['display_order' => 'integer', 'status' => AccessStatus::class];
    }

    /** @return BelongsTo<Module, $this> */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /** @return HasMany<ModuleFeature, $this> */
    public function features(): HasMany
    {
        return $this->hasMany(ModuleFeature::class, 'feature_group_id')->orderBy('display_order');
    }
}
