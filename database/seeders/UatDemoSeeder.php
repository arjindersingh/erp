<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class UatDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([UatOrganisationSeeder::class, UatAcademicSeeder::class, UatWorkforceSeeder::class, UatUserSeeder::class, UatAdmissionsSeeder::class, UatApplicantSeeder::class]);
    }
}
