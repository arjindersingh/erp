<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\System\HealthStatus;
use App\Core\System\SystemHealthService;
use App\Core\System\SystemVersion;
use App\Core\Tenancy\CurrentTenant;
use App\Core\Tenancy\SubscriptionStatus;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Core\Tenancy\TenantDomain;
use App\Core\Tenancy\TenantSubscription;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\ResolvePublicTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use LogicException;
use Tests\TestCase;

class SystemBootTenantFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_detailed_system_health_checks_core_dependencies_without_exposing_exceptions(): void
    {
        $report = app(SystemHealthService::class)->inspect();

        $this->assertNotSame(HealthStatus::Unhealthy, $report->status);
        $this->assertSame(
            ['application', 'database', 'cache', 'queue', 'storage', 'mail', 'environment', 'migrations', 'php', 'logs'],
            $report->checks->pluck('key')->all(),
        );
        $this->assertSame('0', $report->checks->firstWhere('key', 'migrations')->value);
    }

    public function test_public_health_endpoint_is_minimal(): void
    {
        $response = $this->getJson('http://localhost/api/health');

        $response->assertOk()->assertJsonStructure(['status', 'service', 'version', 'timestamp']);
        $response->assertJsonMissingPath('environment');
        $response->assertJsonMissingPath('database');
    }

    public function test_detailed_health_dashboard_is_not_public(): void
    {
        $this->get(route('site-admin.core.health.show'))
            ->assertRedirect(route('home'));
    }

    public function test_system_version_has_a_safe_external_identifier(): void
    {
        $version = SystemVersion::factory()->create();

        $this->assertNotNull($version->uuid);
        $this->assertSame('uuid', $version->getRouteKeyName());
    }

    public function test_verified_active_domain_resolves_and_activates_one_context_instance(): void
    {
        Route::middleware([ResolvePublicTenant::class, EnsureTenantIsActive::class])
            ->get('/context-instance-test', function () {
                $context = app(TenantContext::class);
                $legacy = app(CurrentTenant::class);

                return response()->json([
                    'tenant_id' => $context->id(),
                    'same_instance' => $context === $legacy,
                    'domain' => $context->domain()?->domain,
                ]);
            });
        $tenant = Tenant::factory()->create();
        TenantDomain::factory()->for($tenant)->create(['domain' => 'active.example.test']);

        $this->getJson('http://active.example.test/context-instance-test')
            ->assertOk()
            ->assertJson(['tenant_id' => $tenant->id, 'same_instance' => true, 'domain' => 'active.example.test']);
    }

    public function test_unverified_domain_does_not_resolve(): void
    {
        $tenant = Tenant::factory()->create();
        TenantDomain::factory()->for($tenant)->unverified()->create(['domain' => 'pending.example.test']);

        $this->getJson('http://pending.example.test/api/health')->assertNotFound();
    }

    public function test_suspended_tenant_is_blocked_after_resolution(): void
    {
        $tenant = Tenant::factory()->suspended()->create();
        TenantDomain::factory()->for($tenant)->create(['domain' => 'suspended.example.test']);

        $this->getJson('http://suspended.example.test/api/health')
            ->assertForbidden()
            ->assertJson(['message' => 'This tenant is not currently available.']);
    }

    public function test_context_rejects_a_domain_from_another_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $domainB = TenantDomain::factory()->create();

        $this->expectException(LogicException::class);
        app(TenantContext::class)->activate($tenantA, $domainB);
    }

    public function test_subscriptions_are_tenant_scoped_and_time_aware(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $active = TenantSubscription::factory()->for($tenantA)->create(['status' => SubscriptionStatus::Active]);
        TenantSubscription::factory()->for($tenantB)->create();

        $this->assertTrue($active->isEffectiveAt());
        $this->assertSame(1, TenantSubscription::query()->forTenant($tenantA)->count());
        $this->assertSame($tenantA->id, $active->tenant->id);
    }

    public function test_tenant_uuid_is_used_for_public_route_binding(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertNotNull($tenant->uuid);
        $this->assertSame('uuid', $tenant->getRouteKeyName());
    }
}
