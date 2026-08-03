<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Modules\Module;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Module> */
class ModuleFactory extends Factory
{
    public function definition(): array
    {
        $code = fake()->unique()->slug(1);

        return [
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => Str::headline($code),
            'display_order' => fake()->numberBetween(1, 100),
            'module_type' => 'administrative',
            'is_core' => false,
            'is_public' => false,
            'supports_academic_year' => false,
            'supports_company_scope' => true,
            'supports_campus_scope' => true,
            'supports_institute_scope' => true,
            'status' => 'active',
        ];
    }
}
