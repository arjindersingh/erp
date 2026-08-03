<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\System\SystemVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SystemVersion> */
final class SystemVersionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'version' => fake()->semver(),
            'build' => 'test',
            'commit_hash' => fake()->sha256(),
            'deployed_at' => now(),
            'metadata' => [],
        ];
    }
}
