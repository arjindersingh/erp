<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class UatWorkforceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('Workforce responsibility UAT data deferred: responsibility/committee/delegation schema is not present.');
    }
}
