<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Models\User;
use App\Shared\Support\HasPublicUuid;
use Database\Factories\SettingValueHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(SettingValueHistoryFactory::class)]
#[Fillable(['uuid', 'setting_value_id', 'old_value_json', 'new_value_json', 'changed_fields_json', 'change_source', 'change_reason', 'changed_by'])]
final class SettingValueHistory extends Model
{
    use HasFactory, HasPublicUuid;

    public $timestamps = false;

    protected $casts = [
        'old_value_json' => AsArrayObject::class,
        'new_value_json' => AsArrayObject::class,
        'changed_fields_json' => AsArrayObject::class,
    ];

    public function value(): BelongsTo
    {
        return $this->belongsTo(SettingValue::class, 'setting_value_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
