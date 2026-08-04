<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Shared\Support\HasPublicUuid;
use Database\Factories\SettingOptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(SettingOptionFactory::class)]
#[Fillable(['uuid', 'setting_option_set_id', 'code', 'label', 'description', 'value_json', 'example', 'metadata_json', 'display_order', 'is_default', 'is_recommended', 'is_system', 'status'])]
final class SettingOption extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'value_json' => AsArrayObject::class,
        'metadata_json' => AsArrayObject::class,
        'is_default' => 'boolean',
        'is_recommended' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function optionSet()
    {
        return $this->belongsTo(SettingOptionSet::class);
    }
}
