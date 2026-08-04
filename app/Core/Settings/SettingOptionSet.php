<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Shared\Support\HasPublicUuid;
use Database\Factories\SettingOptionSetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(SettingOptionSetFactory::class)]
#[Fillable(['uuid', 'code', 'name', 'description', 'value_type', 'supports_translations', 'is_system', 'status'])]
final class SettingOptionSet extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'supports_translations' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(SettingOption::class);
    }

    public function definitions()
    {
        return $this->hasMany(SettingDefinition::class, 'setting_option_set_id');
    }
}
