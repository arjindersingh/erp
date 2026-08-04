<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\SettingOption;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SettingOption> */
class SettingOptionFactory extends Factory
{
    protected $model = SettingOption::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->slug(2, false);

        return [
            'uuid' => (string) Str::uuid(),
            'setting_option_set_id' => null,
            'code' => $code,
            'label' => str($code)->headline()->toString(),
            'description' => $this->faker->sentence(),
            'value_json' => ['value' => $this->faker->word()],
            'example' => null,
            'metadata_json' => [],
            'display_order' => 100,
            'is_default' => false,
            'is_recommended' => false,
            'is_system' => true,
            'status' => 'active',
        ];
    }
}
