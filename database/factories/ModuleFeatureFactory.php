<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeature;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ModuleFeature> */
class ModuleFeatureFactory extends Factory
{
    public function definition(): array
    {
        $code = fake()->unique()->slug(1);

        return [
            'module_id' => Module::factory(),
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => Str::headline($code),
            'display_order' => fake()->numberBetween(1, 100),
            'feature_type' => 'resource',
            'supports_search' => true,
            'supports_favourites' => true,
            'supports_quick_action' => false,
            'status' => 'active',
        ];
    }
}
