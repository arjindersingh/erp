<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\SettingOptionSet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SettingOptionSet> */
class SettingOptionSetFactory extends Factory
{
    protected $model = SettingOptionSet::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->slug(2, false);

        return [
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => str($code)->headline()->toString(),
            'description' => $this->faker->sentence(),
            'value_type' => 'string',
            'supports_translations' => false,
            'is_system' => true,
            'status' => 'active',
        ];
    }
}
