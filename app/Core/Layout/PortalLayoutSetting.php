<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\PortalLayoutSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(PortalLayoutSettingFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'portal_id', 'layout_preset_id', 'sidebar_position', 'sidebar_state', 'header_mode', 'topbar_mode', 'content_width', 'footer_mode', 'configuration_json', 'status'])]
final class PortalLayoutSetting extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'configuration_json' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
