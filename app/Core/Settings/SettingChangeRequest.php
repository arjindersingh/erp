<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\SettingChangeRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(SettingChangeRequestFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'setting_definition_id', 'scope_type', 'scope_id', 'current_value_json', 'proposed_value_json', 'reason', 'requested_by', 'reviewed_by', 'decision', 'review_remarks', 'reviewed_at', 'status'])]
final class SettingChangeRequest extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'current_value_json' => AsArrayObject::class,
        'proposed_value_json' => AsArrayObject::class,
        'reviewed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(SettingDefinition::class, 'setting_definition_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
