<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\TenantLayoutSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(TenantLayoutSettingFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'default_layout_preset_id', 'allow_user_sidebar_position', 'allow_user_sidebar_state', 'allow_user_header_mode', 'allow_user_topbar_clock', 'allow_user_content_width', 'allow_user_theme', 'allow_user_font', 'allow_user_density', 'allow_user_footer_mode', 'mandatory_header_context', 'mandatory_footer', 'mandatory_branding', 'configuration_json', 'status', 'created_by', 'updated_by'])]
final class TenantLayoutSetting extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'allow_user_sidebar_position' => 'boolean',
        'allow_user_sidebar_state' => 'boolean',
        'allow_user_header_mode' => 'boolean',
        'allow_user_topbar_clock' => 'boolean',
        'allow_user_content_width' => 'boolean',
        'allow_user_theme' => 'boolean',
        'allow_user_font' => 'boolean',
        'allow_user_density' => 'boolean',
        'allow_user_footer_mode' => 'boolean',
        'mandatory_header_context' => 'array',
        'mandatory_footer' => 'array',
        'mandatory_branding' => 'array',
        'configuration_json' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
