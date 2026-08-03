<?php

declare(strict_types=1);

namespace App\Core\Navigation;

use App\Core\Authorization\AccessStatus;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\MenuSetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(MenuSetFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'portal_id', 'name', 'code', 'description', 'menu_type', 'is_default', 'is_system', 'status', 'created_by', 'updated_by'])]
class MenuSet extends Model
{
    /** @use HasFactory<MenuSetFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = ['menu_type' => MenuType::Sidebar->value, 'is_default' => false, 'is_system' => false, 'status' => AccessStatus::Active->value];

    protected function casts(): array
    {
        return ['menu_type' => MenuType::class, 'is_default' => 'boolean', 'is_system' => 'boolean', 'status' => AccessStatus::class];
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** @return BelongsTo<Portal, $this> */
    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    /** @return HasMany<MenuItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('display_order');
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
}
