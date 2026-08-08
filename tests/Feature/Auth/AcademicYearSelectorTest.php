<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Core\Identity\UserMembership;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use Database\Seeders\UatDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AcademicYearSelectorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        putenv('UAT_TEMP_PASSWORD=Uat-Test-Password-2027!');
        $this->seed();
        $this->seed(UatDemoSeeder::class);
    }

    public function test_dashboard_session_selector_switches_the_active_academic_year(): void
    {
        $this->post('http://uat-a.erp-uat.test/login', [
            'email' => 'admissions.uat-a@erp-uat.test',
            'password' => 'Uat-Test-Password-2027!',
        ]);

        $tenant = Tenant::query()->where('code', 'UAT-A')->firstOrFail();
        $membership = UserMembership::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->whereHas('user', fn ($query) => $query->where('email', 'admissions.uat-a@erp-uat.test'))
            ->firstOrFail();
        $year = AcademicYear::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('code', '2027-2028')
            ->firstOrFail();

        $this->get('http://uat-a.erp-uat.test/dashboard')
            ->assertOk()
            ->assertSee('Academic session')
            ->assertSee($year->name);

        $this->get('http://uat-a.erp-uat.test/dashboard')->assertOk();

        $this->post('http://uat-a.erp-uat.test/context/academic-year', ['academic_year' => $year->uuid])
            ->assertRedirect('http://uat-a.erp-uat.test/dashboard')
            ->assertSessionHas('active_context', [
                'membership_uuid' => $membership->uuid,
                'portal_code' => 'administration',
                'academic_year_uuid' => $year->uuid,
            ]);
    }
}
