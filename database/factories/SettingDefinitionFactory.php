<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\SettingDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SettingDefinition> */
class SettingDefinitionFactory extends Factory
{
    protected $model = SettingDefinition::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->slug(3, false);

        return [
            'uuid' => (string) Str::uuid(),
            'setting_group_id' => null,
            'setting_option_set_id' => null,
            'key' => "{$code}.key",
            'name' => str($code)->headline()->toString(),
            'description' => $this->faker->sentence(),
            'help_text' => $this->faker->paragraph(),
            'value_type' => 'string',
            'default_value_json' => ['value' => null],
            'allowed_scopes_json' => ['platform', 'tenant'],
            'validation_rules_json' => [],
            'allowed_values_json' => [],
            'ui_component' => 'text',
            'placeholder' => null,
            'display_order' => 100,
            'is_required' => false,
            'is_secret' => false,
            'is_encrypted' => false,
            'is_inheritable' => true,
            'is_cacheable' => true,
            'is_user_overridable' => false,
            'requires_approval' => false,
            'requires_restart' => false,
            'is_system' => true,
            'status' => 'active',
        ];
    }
}
