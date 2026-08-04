<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Core\Tenancy\Tenant;
use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\SettingValueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(SettingValueFactory::class)]
#[Fillable(['uuid', 'tenant_id', 'setting_definition_id', 'scope_type', 'scope_id', 'value_json', 'effective_from', 'effective_until', 'status', 'created_by', 'updated_by', 'approved_by', 'approved_at'])]
final class SettingValue extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'value_json' => AsArrayObject::class,
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(SettingDefinition::class, 'setting_definition_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(SettingValueHistory::class, 'setting_value_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
