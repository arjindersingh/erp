<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\UserInterfacePreferenceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(UserInterfacePreferenceFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'user_id', 'portal_id', 'theme_preset_id', 'appearance_mode', 'font_family_id', 'font_scale', 'line_height', 'primary_palette_id', 'interface_density', 'sidebar_mode', 'navigation_style', 'content_width', 'card_radius', 'table_density', 'default_rows_per_page', 'sticky_table_header', 'striped_table_rows', 'wrap_table_text', 'remember_filters', 'remember_sorting', 'remember_visible_columns', 'high_contrast', 'reduced_motion', 'enhanced_focus', 'large_click_targets', 'underline_links', 'dyslexia_friendly_font', 'reduced_transparency', 'simplified_layout', 'dashboard_preferences_json', 'additional_preferences_json', 'preference_version'])]
final class UserInterfacePreference extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'sticky_table_header' => 'boolean',
        'striped_table_rows' => 'boolean',
        'wrap_table_text' => 'boolean',
        'remember_filters' => 'boolean',
        'remember_sorting' => 'boolean',
        'remember_visible_columns' => 'boolean',
        'high_contrast' => 'boolean',
        'reduced_motion' => 'boolean',
        'enhanced_focus' => 'boolean',
        'large_click_targets' => 'boolean',
        'underline_links' => 'boolean',
        'dyslexia_friendly_font' => 'boolean',
        'reduced_transparency' => 'boolean',
        'simplified_layout' => 'boolean',
        'dashboard_preferences_json' => 'array',
        'additional_preferences_json' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
