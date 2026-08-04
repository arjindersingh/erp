<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Platform\ReferenceDataImportService;
use App\Core\Platform\ReferenceGroup;
use Illuminate\Database\Seeder;

class PlatformFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $group = ReferenceGroup::query()->updateOrCreate(
            ['code' => 'genders'],
            [
                'uuid' => '11111111-1111-1111-1111-111111111111',
                'name' => 'Genders',
                'description' => 'Core gender reference values for shared platform operations.',
                'value_type' => 'string',
                'allows_tenant_values' => true,
                'is_system' => true,
                'status' => 'active',
            ],
        );

        if ($group->wasRecentlyCreated || $group->wasChanged()) {
            app(ReferenceDataImportService::class)->import('genders', [
                ['code' => 'male', 'label' => 'Male'],
                ['code' => 'female', 'label' => 'Female'],
                ['code' => 'other', 'label' => 'Other'],
            ]);
        }
    }
}
