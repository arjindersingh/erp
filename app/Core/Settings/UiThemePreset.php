<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\UiThemePresetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(UiThemePresetFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'code', 'name', 'description', 'appearance_mode', 'primary_palette_id', 'secondary_palette_id', 'surface_palette_id', 'font_family_id', 'font_scale', 'line_height', 'interface_density', 'sidebar_mode', 'navigation_style', 'content_width', 'card_radius', 'token_overrides_json', 'is_system', 'is_public', 'is_active', 'created_by', 'updated_by', 'approved_by', 'approved_at'])]
final class UiThemePreset extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'token_overrides_json' => 'array',
        'is_system' => 'boolean',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
