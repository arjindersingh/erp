<?php

declare(strict_types=1);

namespace App\Core\Navigation;

use App\Core\Authorization\AccessStatus;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\PortalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(PortalFactory::class)]
#[Fillable(['uuid', 'code', 'name', 'description', 'icon', 'default_route_name', 'requires_authentication', 'status'])]
class Portal extends Model
{
    /** @use HasFactory<PortalFactory> */
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $attributes = ['requires_authentication' => true, 'status' => AccessStatus::Active->value];

    protected function casts(): array
    {
        return ['requires_authentication' => 'boolean', 'status' => AccessStatus::class];
    }

    /** @return HasMany<MenuSet, $this> */
    public function menuSets(): HasMany
    {
        return $this->hasMany(MenuSet::class);
    }
}
