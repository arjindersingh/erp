<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\UiColourPalette;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<UiColourPalette> */
final class UiColourPaletteFactory extends Factory
{
    protected $model = UiColourPalette::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'code' => fake()->unique()->word(),
            'name' => Str::headline(fake()->word()),
            'description' => fake()->sentence(),
            'shade_50' => '#eff6ff',
            'shade_100' => '#dbeafe',
            'shade_200' => '#bfdbfe',
            'shade_300' => '#93c5fd',
            'shade_400' => '#60a5fa',
            'shade_500' => '#3b82f6',
            'shade_600' => '#2563eb',
            'shade_700' => '#1d4ed8',
            'shade_800' => '#1e40af',
            'shade_900' => '#1e3a8a',
            'shade_950' => '#172554',
            'contrast_text_light' => '#ffffff',
            'contrast_text_dark' => '#0f172a',
            'is_system' => false,
            'is_active' => true,
        ];
    }
}
