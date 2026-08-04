<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class UatApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('Applicant UAT journeys are deferred to the secure-resume frontend milestone.');
    }
}
