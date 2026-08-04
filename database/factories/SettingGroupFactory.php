<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Settings\SettingGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SettingGroup> */
class SettingGroupFactory extends Factory
{
    protected $model = SettingGroup::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->slug(2, false);

        return [
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => str($code)->headline()->toString(),
            'description' => $this->faker->sentence(),
            'icon' => null,
            'display_order' => $this->faker->numberBetween(100, 999),
            'is_system' => true,
            'status' => 'active',
        ];
    }
}
