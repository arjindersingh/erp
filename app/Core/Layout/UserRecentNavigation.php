<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Modules\Module;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\UserRecentNavigationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(UserRecentNavigationFactory::class)]
#[Fillable(['user_id', 'tenant_id', 'portal_id', 'module_id', 'route_name', 'record_type', 'record_id', 'visited_at'])]
final class UserRecentNavigation extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'visited_at' => 'datetime',
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
}
