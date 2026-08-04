<?php

declare(strict_types=1);

namespace App\Core\Settings;

use App\Shared\Support\HasPublicUuid;
use Database\Factories\UiColourPaletteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(UiColourPaletteFactory::class)]
#[Fillable(['uuid', 'code', 'name', 'description', 'shade_50', 'shade_100', 'shade_200', 'shade_300', 'shade_400', 'shade_500', 'shade_600', 'shade_700', 'shade_800', 'shade_900', 'shade_950', 'contrast_text_light', 'contrast_text_dark', 'is_system', 'is_active', 'created_by', 'updated_by'])]
final class UiColourPalette extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];
}
