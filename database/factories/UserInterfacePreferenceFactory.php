<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\UserInterfacePreference;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<UserInterfacePreference> */
final class UserInterfacePreferenceFactory extends Factory
{
    protected $model = UserInterfacePreference::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'appearance_mode' => 'system',
            'font_scale' => '1.00',
            'line_height' => 'normal',
            'interface_density' => 'comfortable',
            'sidebar_mode' => 'auto',
            'navigation_style' => 'sidebar',
            'content_width' => 'standard',
            'card_radius' => 'soft',
            'table_density' => 'comfortable',
            'default_rows_per_page' => 25,
            'sticky_table_header' => false,
            'striped_table_rows' => true,
            'wrap_table_text' => true,
            'remember_filters' => false,
            'remember_sorting' => false,
            'remember_visible_columns' => false,
            'high_contrast' => false,
            'reduced_motion' => false,
            'enhanced_focus' => false,
            'large_click_targets' => false,
            'underline_links' => false,
            'dyslexia_friendly_font' => false,
            'reduced_transparency' => false,
            'simplified_layout' => false,
            'dashboard_preferences_json' => [],
            'additional_preferences_json' => [],
            'preference_version' => 1,
        ];
    }
}
