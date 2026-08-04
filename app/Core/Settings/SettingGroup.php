<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Shared\Support\HasPublicUuid;
use Database\Factories\SettingGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(SettingGroupFactory::class)]
#[Fillable(['uuid', 'code', 'name', 'description', 'icon', 'display_order', 'is_system', 'status'])]
final class SettingGroup extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function definitions()
    {
        return $this->hasMany(SettingDefinition::class, 'setting_group_id');
    }
}
