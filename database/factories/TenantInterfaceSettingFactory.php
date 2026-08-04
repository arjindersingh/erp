<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\TenantInterfaceSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<TenantInterfaceSetting> */
final class TenantInterfaceSettingFactory extends Factory
{
    protected $model = TenantInterfaceSetting::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'brand_name' => fake()->company(),
            'short_brand_name' => fake()->companySuffix(),
            'login_layout' => 'default',
            'header_style' => 'default',
            'sidebar_style' => 'default',
            'footer_style' => 'default',
            'allow_user_appearance_mode' => true,
            'allow_user_theme_selection' => true,
            'allow_user_font_selection' => true,
            'allow_user_font_scale' => true,
            'allow_user_palette_selection' => true,
            'allow_user_density_selection' => true,
            'allow_user_sidebar_selection' => true,
            'allow_user_content_width' => true,
            'allow_user_table_preferences' => true,
            'allow_user_dashboard_preferences' => true,
            'allow_user_accessibility_preferences' => true,
            'status' => 'active',
        ];
    }
}
