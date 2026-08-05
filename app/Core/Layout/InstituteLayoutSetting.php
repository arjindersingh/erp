<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\InstituteLayoutSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(InstituteLayoutSettingFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'institute_id', 'layout_preset_id', 'header_title', 'header_subtitle', 'show_institute_logo', 'show_academic_year', 'show_campus_name', 'configuration_json', 'status'])]
final class InstituteLayoutSetting extends Model
{
    use HasFactory, HasPublicUuid;

    protected $casts = [
        'show_institute_logo' => 'boolean',
        'show_academic_year' => 'boolean',
        'show_campus_name' => 'boolean',
        'configuration_json' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }
}
