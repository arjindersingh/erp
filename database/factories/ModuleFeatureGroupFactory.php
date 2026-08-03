<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeatureGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ModuleFeatureGroup> */
class ModuleFeatureGroupFactory extends Factory
{
    public function definition(): array
    {
        $code = fake()->unique()->slug(1);

        return ['module_id' => Module::factory(), 'uuid' => (string) Str::uuid(), 'code' => $code, 'name' => Str::headline($code), 'display_order' => 0, 'status' => 'active'];
    }
}
