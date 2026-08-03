<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Navigation\MenuSet;
use App\Core\Navigation\Portal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<MenuSet> */
class MenuSetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tenant_id' => null,
            'portal_id' => Portal::factory(),
            'name' => fake()->words(2, true),
            'code' => fake()->unique()->slug(2),
            'menu_type' => 'sidebar',
            'is_default' => false,
            'is_system' => false,
            'status' => 'active',
        ];
    }
}
