<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Navigation\Portal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Portal> */
class PortalFactory extends Factory
{
    public function definition(): array
    {
        $code = fake()->unique()->slug(1);

        return ['uuid' => (string) Str::uuid(), 'code' => $code, 'name' => Str::headline($code), 'requires_authentication' => true, 'status' => 'active'];
    }
}
