<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Shared\Support\HasPublicUuid;
use Database\Factories\UiFontFamilyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(UiFontFamilyFactory::class)]
#[Fillable(['uuid', 'code', 'name', 'css_font_family', 'fallback_family', 'available_weights_json', 'is_dyslexia_friendly', 'is_system', 'is_active', 'created_by', 'updated_by'])]
final class UiFontFamily extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'available_weights_json' => 'array',
        'is_dyslexia_friendly' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];
}
