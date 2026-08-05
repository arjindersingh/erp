<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Modules\Module;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\ModuleLayoutSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(ModuleLayoutSettingFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'module_id', 'portal_id', 'layout_preset_id', 'preferred_sidebar_state', 'preferred_content_width', 'show_module_quick_actions', 'show_module_search', 'show_module_dashboard_widgets', 'configuration_json', 'status'])]
final class ModuleLayoutSetting extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'show_module_quick_actions' => 'boolean',
        'show_module_search' => 'boolean',
        'show_module_dashboard_widgets' => 'boolean',
        'configuration_json' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
