<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\TenantInterfaceSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(TenantInterfaceSettingFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'brand_name', 'short_brand_name', 'logo_document_id', 'compact_logo_document_id', 'favicon_document_id', 'login_background_document_id', 'default_theme_preset_id', 'primary_palette_id', 'secondary_palette_id', 'login_layout', 'header_style', 'sidebar_style', 'footer_style', 'allow_user_appearance_mode', 'allow_user_theme_selection', 'allow_user_font_selection', 'allow_user_font_scale', 'allow_user_palette_selection', 'allow_user_density_selection', 'allow_user_sidebar_selection', 'allow_user_content_width', 'allow_user_table_preferences', 'allow_user_dashboard_preferences', 'allow_user_accessibility_preferences', 'minimum_font_scale', 'maximum_font_scale', 'status', 'created_by', 'updated_by'])]
final class TenantInterfaceSetting extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'allow_user_appearance_mode' => 'boolean',
        'allow_user_theme_selection' => 'boolean',
        'allow_user_font_selection' => 'boolean',
        'allow_user_font_scale' => 'boolean',
        'allow_user_palette_selection' => 'boolean',
        'allow_user_density_selection' => 'boolean',
        'allow_user_sidebar_selection' => 'boolean',
        'allow_user_content_width' => 'boolean',
        'allow_user_table_preferences' => 'boolean',
        'allow_user_dashboard_preferences' => 'boolean',
        'allow_user_accessibility_preferences' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
