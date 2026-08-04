<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\UiFontFamily;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<UiFontFamily> */
final class UiFontFamilyFactory extends Factory
{
    protected $model = UiFontFamily::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'code' => fake()->unique()->word(),
            'name' => fake()->word(),
            'css_font_family' => 'ui-sans-serif, system-ui, sans-serif',
            'fallback_family' => 'sans-serif',
            'available_weights_json' => ['400', '500', '600'],
            'is_dyslexia_friendly' => false,
            'is_system' => false,
            'is_active' => true,
        ];
    }
}
