<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use Illuminate\Database\Seeder;

final class UatAcademicSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Tenant::query()->whereIn('code', ['UAT-A', 'UAT-B'])->get() as $tenant) {
            AcademicYear::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'code' => '2027-2028'], ['name' => '2027–28', 'starts_on' => '2027-04-01', 'ends_on' => '2028-03-31', 'is_current' => true, 'is_default' => true, 'status' => 'active']);
        }
    }
}
