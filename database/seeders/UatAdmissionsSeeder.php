<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Database\Seeder;

final class UatAdmissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Tenant::query()->whereIn('code', ['UAT-A', 'UAT-B'])->get() as $tenant) {
            $institute = Institute::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('code', 'like', '%-SCHOOL')->firstOrFail();
            $year = AcademicYear::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('code', '2027-2028')->firstOrFail();
            AdmissionCampaign::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'institute_id' => $institute->id, 'academic_year_id' => $year->id, 'code' => 'ADM-2027'], ['company_id' => $institute->company_id, 'campus_id' => $institute->campus_id, 'name' => 'School Admissions 2027–28', 'application_opens_at' => now()->subDay(), 'application_closes_at' => now()->addMonth(), 'submission_deadline_at' => now()->addMonth(), 'status' => 'open']);
        }
    }
}
