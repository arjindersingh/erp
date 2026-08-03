<?php

declare(strict_types=1);

namespace App\Core\Navigation;

use App\Core\Authorization\AccessStatus;
use App\Core\Authorization\Permission;
use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeature;
use App\Core\Navigation\Exceptions\InvalidMenuItem;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(MenuItemFactory::class)]
#[Fillable(['uuid', 'menu_set_id', 'parent_id', 'module_id', 'module_feature_id', 'title', 'short_title', 'description', 'route_name', 'route_parameters_json', 'external_url', 'icon', 'badge_type', 'badge_source', 'display_order', 'depth', 'item_type', 'target', 'permission_code', 'requires_any_permission_json', 'requires_all_permissions_json', 'visible_when_json', 'is_collapsible', 'is_expanded_by_default', 'is_favourite_allowed', 'is_searchable', 'is_system', 'status'])]
class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = [
        'display_order' => 0,
        'depth' => 0,
        'item_type' => MenuItemType::Link->value,
        'target' => LinkTarget::SameWindow->value,
        'is_collapsible' => false,
        'is_expanded_by_default' => false,
        'is_favourite_allowed' => true,
        'is_searchable' => true,
        'is_system' => false,
        'status' => AccessStatus::Active->value,
    ];

    protected static function booted(): void
    {
        static::saving(function (MenuItem $item): void {
            if ($item->route_name !== null && $item->external_url !== null) {
                throw InvalidMenuItem::because('a route and external URL cannot both be configured.');
            }

            if ($item->external_url !== null && ! filter_var($item->external_url, FILTER_VALIDATE_URL)) {
                throw InvalidMenuItem::because('external URL is malformed.');
            }

            if ($item->external_url !== null && ! in_array(parse_url($item->external_url, PHP_URL_SCHEME), ['http', 'https'], true)) {
                throw InvalidMenuItem::because('external URL must use HTTP or HTTPS.');
            }

            if ($item->permission_code !== null && ! Permission::query()->where('code', $item->permission_code)->exists()) {
                throw InvalidMenuItem::because('permission code does not exist.');
            }

            if ($item->module_feature_id !== null
                && ! ModuleFeature::query()->whereKey($item->module_feature_id)->where('module_id', $item->module_id)->exists()) {
                throw InvalidMenuItem::because('feature must belong to the selected module.');
            }

            $parent = $item->parent_id === null ? null : self::query()->find($item->parent_id);
            if ($parent === null) {
                if ($item->parent_id !== null) {
                    throw InvalidMenuItem::because('parent does not exist or is inactive.');
                }

                $item->depth = 0;

                return;
            }

            if ($parent->menu_set_id !== (int) $item->menu_set_id || ! $parent->item_type->mayContainChildren()) {
                throw InvalidMenuItem::because('parent must be a group in the same menu set.');
            }

            if ($item->exists && ($parent->is($item) || $parent->isDescendantOf($item))) {
                throw InvalidMenuItem::because('circular parent relationships are not allowed.');
            }

            $item->depth = $parent->depth + 1;
            if ($item->depth > 2) {
                throw InvalidMenuItem::because('menu depth cannot exceed three levels.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'route_parameters_json' => 'array', 'requires_any_permission_json' => 'array',
            'requires_all_permissions_json' => 'array', 'visible_when_json' => 'array',
            'display_order' => 'integer', 'depth' => 'integer', 'item_type' => MenuItemType::class,
            'target' => LinkTarget::class, 'is_collapsible' => 'boolean',
            'is_expanded_by_default' => 'boolean', 'is_favourite_allowed' => 'boolean',
            'is_searchable' => 'boolean', 'is_system' => 'boolean', 'status' => AccessStatus::class,
        ];
    }

    /** @return BelongsTo<MenuSet, $this> */
    public function menuSet(): BelongsTo
    {
        return $this->belongsTo(MenuSet::class);
    }

    /** @return BelongsTo<MenuItem, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<MenuItem, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('display_order');
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

    public function isDescendantOf(MenuItem $candidate): bool
    {
        $parent = $this->parent;
        while ($parent !== null) {
            if ($parent->is($candidate)) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }
}
