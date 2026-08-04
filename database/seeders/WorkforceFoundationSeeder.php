<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Workforce\Models\DepartmentType;
use App\Domains\Workforce\Models\DesignationCategory;
use App\Domains\Workforce\Models\EmploymentStatus;
use App\Domains\Workforce\Models\EmploymentType;
use App\Domains\Workforce\Models\JobCategory;
use Illuminate\Database\Seeder;

final class WorkforceFoundationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['ACADEMIC', 'Academic Department', true], ['ADMINISTRATIVE', 'Administrative Department', false], ['FINANCE', 'Finance Department', false], ['EXAMINATION', 'Examination Branch', false], ['ADMISSIONS', 'Admissions Office', false], ['SUPPORT', 'Support Services', false]] as [$code, $name, $academic]) {
            DepartmentType::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => $code], ['name' => $name, 'is_academic' => $academic, 'is_system' => true, 'status' => 'active']);
        }
        foreach (['Teaching', 'Administrative', 'Clerical', 'Technical', 'Support', 'Management', 'Contractual'] as $sequence => $name) {
            DesignationCategory::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => str($name)->snake()->upper()->toString()], ['name' => $name, 'sequence' => ($sequence + 1) * 10, 'is_system' => true, 'status' => 'active']);
        }
        foreach (['Teaching Staff', 'Non-Teaching Staff', 'Administrative Staff', 'Management', 'Technical Staff', 'Support Staff'] as $name) {
            JobCategory::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => str($name)->snake()->upper()->toString()], ['name' => $name, 'is_system' => true, 'status' => 'active']);
        }
        foreach (['Regular', 'Probation', 'Contract', 'Part-Time', 'Visiting', 'Guest Faculty', 'Temporary', 'Daily Wage', 'Consultant', 'Deputation'] as $name) {
            EmploymentType::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => str($name)->snake()->upper()->toString()], ['name' => $name, 'is_system' => true, 'status' => 'active']);
        }
        foreach ([['Active', true, false], ['On Probation', true, false], ['On Leave', true, false], ['Suspended', false, false], ['Transferred', false, true], ['Resigned', false, true], ['Retired', false, true], ['Terminated', false, true], ['Deceased', false, true], ['Contract Completed', false, true]] as [$name, $active, $terminal]) {
            EmploymentStatus::withoutGlobalScopes()->updateOrCreate(['tenant_id' => null, 'code' => str($name)->snake()->upper()->toString()], ['name' => $name, 'is_active_status' => $active, 'is_terminal_status' => $terminal, 'allows_teaching_assignment' => $active, 'allows_system_access' => $active, 'is_system' => true, 'status' => 'active']);
        }
    }
}
