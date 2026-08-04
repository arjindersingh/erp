<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\UiThemePreset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<UiThemePreset> */
final class UiThemePresetFactory extends Factory
{
    protected $model = UiThemePreset::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'code' => fake()->unique()->slug(1),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'appearance_mode' => 'system',
            'font_scale' => '1.00',
            'line_height' => 'normal',
            'interface_density' => 'comfortable',
            'sidebar_mode' => 'auto',
            'navigation_style' => 'sidebar',
            'content_width' => 'standard',
            'card_radius' => 'soft',
            'token_overrides_json' => [],
            'is_system' => false,
            'is_public' => true,
            'is_active' => true,
        ];
    }
}
