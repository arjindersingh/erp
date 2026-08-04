<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\PortalInterfaceSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<PortalInterfaceSetting> */
final class PortalInterfaceSettingFactory extends Factory
{
    protected $model = PortalInterfaceSetting::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'default_appearance_mode' => 'system',
            'default_font_scale' => '1.00',
            'default_density' => 'comfortable',
            'default_sidebar_mode' => 'auto',
            'default_navigation_style' => 'sidebar',
            'default_content_width' => 'standard',
            'show_global_search' => true,
            'show_breadcrumbs' => true,
            'show_notifications' => true,
            'show_context_switcher' => true,
            'show_profile_photo' => true,
            'show_footer' => true,
            'status' => 'active',
        ];
    }
}
