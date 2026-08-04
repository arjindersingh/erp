<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\PortalInterfaceSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(PortalInterfaceSettingFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'portal_id', 'default_theme_preset_id', 'default_font_family_id', 'default_appearance_mode', 'default_font_scale', 'default_density', 'default_sidebar_mode', 'default_navigation_style', 'default_content_width', 'show_global_search', 'show_breadcrumbs', 'show_notifications', 'show_context_switcher', 'show_profile_photo', 'show_footer', 'allowed_theme_presets_json', 'allowed_font_families_json', 'allowed_colour_palettes_json', 'allowed_font_scales_json', 'allowed_density_modes_json', 'allowed_sidebar_modes_json', 'allowed_content_widths_json', 'status', 'created_by', 'updated_by'])]
final class PortalInterfaceSetting extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'show_global_search' => 'boolean',
        'show_breadcrumbs' => 'boolean',
        'show_notifications' => 'boolean',
        'show_context_switcher' => 'boolean',
        'show_profile_photo' => 'boolean',
        'show_footer' => 'boolean',
        'allowed_theme_presets_json' => 'array',
        'allowed_font_families_json' => 'array',
        'allowed_colour_palettes_json' => 'array',
        'allowed_font_scales_json' => 'array',
        'allowed_density_modes_json' => 'array',
        'allowed_sidebar_modes_json' => 'array',
        'allowed_content_widths_json' => 'array',
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
