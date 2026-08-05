<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Navigation\MenuItem;
use App\Core\Modules\Module;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\UserSidebarItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(UserSidebarItemFactory::class)]
#[Fillable(['user_id', 'tenant_id', 'portal_id', 'module_id', 'menu_item_id', 'is_pinned', 'is_favourite', 'custom_order', 'is_hidden_by_user'])]
final class UserSidebarItem extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_favourite' => 'boolean',
        'is_hidden_by_user' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
