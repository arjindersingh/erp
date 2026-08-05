<?php

declare(strict_types=1);

namespace App\Core\Layout;

use App\Core\Tenancy\Tenant;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\LayoutPresetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(LayoutPresetFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'code', 'name', 'description', 'sidebar_position', 'sidebar_state', 'sidebar_width', 'header_mode', 'topbar_mode', 'content_width', 'footer_mode', 'density', 'configuration_json', 'is_system', 'status'])]
final class LayoutPreset extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'configuration_json' => 'array',
        'is_system' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
