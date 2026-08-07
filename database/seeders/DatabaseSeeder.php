<?php

namespace Database\Seeders;

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
            PlatformFoundationSeeder::class,
            CoreModuleSeeder::class,
            CorePermissionSeeder::class,
            AcademicFoundationSeeder::class,
            AdmissionsFoundationSeeder::class,
            WorkforceFoundationSeeder::class,
            StudentGuardianFoundationSeeder::class,
            SystemRoleSeeder::class,
            NavigationFoundationSeeder::class,
            SystemUserSeeder::class,
            AuditFoundationSeeder::class,
            SettingFoundationSeeder::class,
        ]);

    }
}
