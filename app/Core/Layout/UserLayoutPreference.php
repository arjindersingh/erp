<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Navigation\Portal;
use App\Core\Organization\Institute;
use App\Core\Modules\Module;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\UserLayoutPreferenceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(UserLayoutPreferenceFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'user_id', 'portal_id', 'module_id', 'layout_preset_id', 'sidebar_position', 'sidebar_state', 'sidebar_width', 'sidebar_auto_collapse', 'header_mode', 'topbar_visible', 'topbar_clock_format', 'show_seconds', 'content_width', 'content_density', 'footer_mode', 'theme_preset_id', 'colour_palette_id', 'font_family_id', 'font_size', 'line_height', 'border_radius', 'table_density', 'form_density', 'show_menu_icons', 'show_breadcrumbs', 'show_quick_actions', 'animation_level', 'reduced_motion', 'high_contrast', 'default_portal_id', 'default_module_id', 'default_institute_id', 'default_academic_year_id'])]
final class UserLayoutPreference extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'topbar_visible' => 'boolean',
        'show_seconds' => 'boolean',
        'show_menu_icons' => 'boolean',
        'show_breadcrumbs' => 'boolean',
        'show_quick_actions' => 'boolean',
        'reduced_motion' => 'boolean',
        'high_contrast' => 'boolean',
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

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function defaultPortal(): BelongsTo
    {
        return $this->belongsTo(Portal::class, 'default_portal_id');
    }

    public function defaultModule(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'default_module_id');
    }

    public function defaultInstitute(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'default_institute_id');
    }

    public function defaultAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'default_academic_year_id');
    }
}
