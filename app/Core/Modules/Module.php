<?php

declare(strict_types=1);

namespace App\Core\Modules;

use App\Core\Authorization\AccessStatus;
use App\Core\Authorization\Permission;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\ModuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(ModuleFactory::class)]
#[Fillable(['uuid', 'code', 'name', 'short_name', 'description', 'icon', 'theme_key', 'route_prefix', 'default_route_name', 'display_order', 'module_type', 'is_core', 'is_public', 'supports_academic_year', 'supports_company_scope', 'supports_campus_scope', 'supports_institute_scope', 'status'])]
class Module extends Model
{
    /** @use HasFactory<ModuleFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'display_order' => 0,
        'module_type' => ModuleType::Administrative->value,
        'is_core' => false,
        'is_public' => false,
        'status' => AccessStatus::Active->value,
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'module_type' => ModuleType::class,
            'is_core' => 'boolean',
            'is_public' => 'boolean',
            'supports_academic_year' => 'boolean',
            'supports_company_scope' => 'boolean',
            'supports_campus_scope' => 'boolean',
            'supports_institute_scope' => 'boolean',
            'status' => AccessStatus::class,
        ];
    }

    /** @return HasMany<ModuleFeature, $this> */
    public function features(): HasMany
    {
        return $this->hasMany(ModuleFeature::class)->orderBy('display_order');
    }

    /** @return HasMany<ModuleFeatureGroup, $this> */
    public function featureGroups(): HasMany
    {
        return $this->hasMany(ModuleFeatureGroup::class)->orderBy('display_order');
    }

    /** @return HasMany<TenantModule, $this> */
    public function tenantConfigurations(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    /** @return HasMany<Permission, $this> */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
