<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\System\SystemVersion;
use Illuminate\Database\Seeder;

final class SystemVersionSeeder extends Seeder
{
    public function run(): void
    {
        SystemVersion::query()->firstOrCreate(
            ['version' => (string) config('system.version'), 'build' => (string) config('system.build')],
            ['commit_hash' => config('system.commit_hash'), 'deployed_at' => now(), 'metadata' => ['php' => PHP_VERSION]],
        );
    }
}
