<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Shared\Support\HasPublicUuid;
use Database\Factories\SettingDefinitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(SettingDefinitionFactory::class)]
#[Fillable(['uuid', 'setting_group_id', 'setting_option_set_id', 'key', 'name', 'description', 'help_text', 'value_type', 'default_value_json', 'allowed_scopes_json', 'validation_rules_json', 'allowed_values_json', 'ui_component', 'placeholder', 'display_order', 'is_required', 'is_secret', 'is_encrypted', 'is_inheritable', 'is_cacheable', 'is_user_overridable', 'requires_approval', 'requires_restart', 'is_system', 'status'])]
final class SettingDefinition extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'default_value_json' => AsArrayObject::class,
        'allowed_scopes_json' => AsArrayObject::class,
        'validation_rules_json' => AsArrayObject::class,
        'allowed_values_json' => AsArrayObject::class,
        'is_required' => 'boolean',
        'is_secret' => 'boolean',
        'is_encrypted' => 'boolean',
        'is_inheritable' => 'boolean',
        'is_cacheable' => 'boolean',
        'is_user_overridable' => 'boolean',
        'requires_approval' => 'boolean',
        'requires_restart' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(SettingGroup::class, 'setting_group_id');
    }

    public function optionSet()
    {
        return $this->belongsTo(SettingOptionSet::class, 'setting_option_set_id');
    }

    public function values()
    {
        return $this->hasMany(SettingValue::class, 'setting_definition_id');
    }

    public function changeRequests()
    {
        return $this->hasMany(SettingChangeRequest::class, 'setting_definition_id');
    }
}
