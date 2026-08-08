<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Core\Identity\UserMembership;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Models\AcademicYear;
use Database\Seeders\UatDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FrontendAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        putenv('UAT_TEMP_PASSWORD=Uat-Test-Password-2027!');
        $this->seed();
        $this->seed(UatDemoSeeder::class);
    }

    public function test_active_uat_user_can_login_only_on_own_tenant_domain(): void
    {
        $this->post('http://uat-a.erp-uat.test/login', ['email' => 'admissions.uat-a@erp-uat.test', 'password' => 'Uat-Test-Password-2027!'])
            ->assertRedirect(route('portal.dashboard'));
        $this->post('http://uat-a.erp-uat.test/logout');
        $this->post('http://uat-b.erp-uat.test/login', ['email' => 'admissions.uat-a@erp-uat.test', 'password' => 'Uat-Test-Password-2027!'])
            ->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_is_sent_to_context_selection_when_revisiting_login(): void
    {
        $this->post('http://uat-a.erp-uat.test/login', [
            'email' => 'admissions.uat-a@erp-uat.test',
            'password' => 'Uat-Test-Password-2027!',
        ])->assertRedirect(route('portal.dashboard'));

        $this->get('http://uat-a.erp-uat.test/login')
            ->assertRedirect(route('portal.dashboard'));

        $this->get('http://uat-a.erp-uat.test/')
            ->assertOk()
            ->assertSee('Open staff portal')
            ->assertSee('Sign out');
    }

    public function test_login_automatically_uses_the_membership_portal_and_current_year(): void
    {
        $tenant = Tenant::query()->where('code', 'UAT-A')->firstOrFail();
        $membership = UserMembership::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->whereHas('user', fn ($query) => $query->where('email', 'admissions.uat-a@erp-uat.test'))
            ->firstOrFail();
        $year = AcademicYear::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('is_current', true)
            ->where('is_default', true)
            ->firstOrFail();

        $this->post('http://uat-a.erp-uat.test/login', [
            'email' => 'admissions.uat-a@erp-uat.test',
            'password' => 'Uat-Test-Password-2027!',
        ])
            ->assertRedirect(route('portal.dashboard'))
            ->assertSessionHas('active_context', [
                'membership_uuid' => $membership->uuid,
                'portal_code' => 'administration',
                'academic_year_uuid' => $year->uuid,
            ]);

        $this->get('http://uat-a.erp-uat.test/dashboard')
            ->assertOk()
            ->assertSee('Administration dashboard')
            ->assertSee('Admissions')
            ->assertDontSee('Transport');
    }

    public function test_site_administrator_uses_the_site_administration_portal(): void
    {
        $this->post('http://demo.erp.test/login', [
            'email' => 'system@erp.local',
            'password' => 'Administrator',
        ])
            ->assertRedirect(route('portal.dashboard'))
            ->assertSessionHas('active_context.portal_code', 'site_admin');
    }

    public function test_context_options_are_server_generated_and_cross_tenant_selection_is_rejected(): void
    {
        $this->post('http://uat-a.erp-uat.test/login', ['email' => 'admissions.uat-a@erp-uat.test', 'password' => 'Uat-Test-Password-2027!']);
        $this->get('http://uat-a.erp-uat.test/context/select')->assertOk()->assertSee('School UAT-A');
        $tenantA = Tenant::query()->where('code', 'UAT-A')->firstOrFail();
        $foreignMembership = UserMembership::withoutGlobalScopes()->where('tenant_id', '<>', $tenantA->id)->firstOrFail();
        $year = AcademicYear::withoutGlobalScopes()->where('tenant_id', $tenantA->id)->where('code', '2027-2028')->firstOrFail();
        $this->post('http://uat-a.erp-uat.test/context/select', ['membership' => $foreignMembership->uuid, 'portal' => 'administration', 'academic_year' => $year->uuid])
            ->assertSessionHasErrors('membership');
    }

    public function test_valid_context_opens_staff_dashboard_and_restricted_diagnostics(): void
    {
        $this->post('http://uat-a.erp-uat.test/login', ['email' => 'admissions.uat-a@erp-uat.test', 'password' => 'Uat-Test-Password-2027!']);
        $tenant = Tenant::query()->where('code', 'UAT-A')->firstOrFail();
        $membership = UserMembership::withoutGlobalScopes()->where('tenant_id', $tenant->id)->firstOrFail();
        $year = AcademicYear::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('code', '2027-2028')->firstOrFail();
        $this->post('http://uat-a.erp-uat.test/context/select', ['membership' => $membership->uuid, 'portal' => 'administration', 'academic_year' => $year->uuid])
            ->assertRedirect(route('portal.dashboard'));
        $this->get('http://uat-a.erp-uat.test/staff/admissions')->assertOk()->assertSee('Admissions dashboard');
        $this->get('http://uat-a.erp-uat.test/admin/access-diagnostics')->assertOk()->assertSee('Reena Sharma')->assertSee('admissions.dashboard.view');
    }
}
