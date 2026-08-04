<?php

declare(strict_types=1);

namespace Tests\Feature\Admissions;

use App\Core\Modules\Module;
use App\Core\Modules\TenantModule;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Core\Tenancy\TenantDomain;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Admissions\Enums\AdmissionApplicationSource;
use App\Domains\Admissions\Models\AdmissionApplication;
use App\Domains\Admissions\Models\AdmissionCampaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdmissionsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_open_campaign_is_accessible_without_login(): void
    {
        [$tenant, $domain] = $this->tenantWithDomain('admissions.example.test');
        $campaign = $this->campaignFor($tenant);

        $this->get("http://{$domain->domain}/admissions/apply/{$campaign->uuid}")
            ->assertOk()->assertSee($campaign->name);
    }

    public function test_closed_campaign_is_not_publicly_accessible(): void
    {
        [$tenant, $domain] = $this->tenantWithDomain('closed.example.test');
        $campaign = $this->campaignFor($tenant, ['status' => 'closed', 'application_closes_at' => now()->subDay()]);

        $this->get("http://{$domain->domain}/admissions/apply/{$campaign->uuid}")->assertNotFound();
    }

    public function test_public_applicant_can_start_a_draft_without_an_account(): void
    {
        [$tenant, $domain] = $this->tenantWithDomain('apply.example.test');
        $campaign = $this->campaignFor($tenant);

        $this->post("http://{$domain->domain}/admissions/apply/{$campaign->uuid}", [
            'given_name' => 'Asha', 'date_of_birth' => '2008-05-10', 'email' => 'ASHA@example.test',
        ])->assertRedirect();

        $application = AdmissionApplication::withoutGlobalScopes()->sole();
        $this->assertSame('public_online', $application->source->value);
        $this->assertSame($tenant->id, $application->tenant_id);
        $this->assertSame('asha@example.test', $application->applicant_email);
        $this->assertNotNull($application->access_token_hash);
    }

    public function test_manual_application_source_and_provenance_are_preserved(): void
    {
        $tenant = Tenant::factory()->create();
        $campaign = $this->campaignFor($tenant);
        app(TenantContext::class)->activate($tenant);

        $application = AdmissionApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'tenant_id' => $tenant->id,
            'source' => AdmissionApplicationSource::ManualPaper,
            'paper_form_number' => 'PAPER-1001',
            'submission_location' => 'North counter',
            'received_at' => now(),
        ]);

        $this->assertSame(AdmissionApplicationSource::ManualPaper, $application->source);
        $this->assertSame('PAPER-1001', $application->paper_form_number);
    }

    public function test_campaign_queries_are_tenant_isolated(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $this->campaignFor($tenantA);
        $this->campaignFor($tenantB);
        app(TenantContext::class)->activate($tenantB);

        $this->assertSame(1, AdmissionCampaign::query()->count());
        $this->assertTrue(AdmissionCampaign::query()->firstOrFail()->tenant_id === $tenantB->id);
    }

    /** @return array{Tenant, TenantDomain} */
    private function tenantWithDomain(string $domain): array
    {
        $tenant = Tenant::factory()->create();
        $tenantDomain = TenantDomain::factory()->create(['tenant_id' => $tenant->id, 'domain' => $domain]);
        $module = Module::query()->firstOrCreate(['code' => 'admissions'], ['name' => 'Admissions', 'status' => 'active']);
        TenantModule::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'module_id' => $module->id, 'is_enabled' => true, 'enabled_at' => now()]);

        return [$tenant, $tenantDomain];
    }

    /** @param array<string, mixed> $attributes */
    private function campaignFor(Tenant $tenant, array $attributes = []): AdmissionCampaign
    {
        app(TenantContext::class)->clear();
        $company = Company::factory()->create(['tenant_id' => $tenant->id]);
        $campus = Campus::factory()->create(['tenant_id' => $tenant->id, 'company_id' => $company->id]);
        $institute = Institute::factory()->create([
            'tenant_id' => $tenant->id, 'company_id' => $company->id, 'campus_id' => $campus->id,
        ]);
        $year = AcademicYear::factory()->create(['tenant_id' => $tenant->id]);

        return AdmissionCampaign::factory()->create(array_merge([
            'tenant_id' => $tenant->id, 'company_id' => $company->id, 'campus_id' => $campus->id,
            'institute_id' => $institute->id, 'academic_year_id' => $year->id,
        ], $attributes));
    }
}
