<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Navigation\MenuItem;
use App\Core\Navigation\MenuSet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<MenuItem> */
class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'menu_set_id' => MenuSet::factory(),
            'title' => fake()->words(2, true),
            'route_name' => 'dashboard',
            'display_order' => 0,
            'item_type' => 'link',
            'target' => 'same_window',
            'is_favourite_allowed' => true,
            'is_searchable' => true,
            'status' => 'active',
        ];
    }
}
