<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SystemVersionSeeder::class,
            TenantFoundationSeeder::class,
            CoreModuleSeeder::class,
            CorePermissionSeeder::class,
            AcademicFoundationSeeder::class,
            WorkforceFoundationSeeder::class,
            SystemRoleSeeder::class,
            NavigationFoundationSeeder::class,
            AuditFoundationSeeder::class,
        ]);

        // User::factory(10)->create();

        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            User::factory()->raw(['name' => 'Test User']),
        );
    }
}
