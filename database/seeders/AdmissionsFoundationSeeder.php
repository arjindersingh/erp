<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Modules\Module;
use Illuminate\Database\Seeder;

final class AdmissionsFoundationSeeder extends Seeder
{
    public function run(): void
    {
        Module::query()->updateOrCreate(['code' => 'admissions'], [
            'name' => 'Admissions', 'route_prefix' => 'admissions',
            'default_route_name' => 'admissions.public.campaigns',
            'module_type' => 'administrative', 'is_public' => true,
            'supports_academic_year' => true, 'supports_company_scope' => true,
            'supports_campus_scope' => true, 'supports_institute_scope' => true, 'status' => 'active',
        ]);
    }
}
