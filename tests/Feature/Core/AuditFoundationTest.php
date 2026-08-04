<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Audit\AuditActorType;
use App\Core\Audit\AuditContext;
use App\Core\Audit\AuditEventDefinition;
use App\Core\Audit\AuditLogger;
use App\Core\Audit\AuditOutcome;
use App\Core\Audit\AuditSource;
use App\Core\Authorization\AccessScope;
use App\Core\Authorization\Role;
use App\Core\Identity\Person;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use Database\Seeders\AuditFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('audit')]
class AuditFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_audit_captures_actor_tenant_scope_portal_role_and_subject(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $scope = AccessScope::factory()->for($tenant)->create();
        $portal = Portal::factory()->create();
        $role = Role::factory()->create();
        $person = Person::factory()->for($tenant)->create(['display_name' => 'Aman Sharma']);
        $this->seed(AuditFoundationSeeder::class);
        $context = new AuditContext(
            tenantId: $tenant->id, actorType: AuditActorType::User, actorId: $user->id,
            userId: $user->id, accessScopeId: $scope->id, roleId: $role->id, portalId: $portal->id,
            requestId: fake()->uuid(), source: AuditSource::Web, routeName: 'admissions.approve',
        );

        $log = app(AuditLogger::class)->withContext($context)->success(
            'admissions.application.approved',
            $person,
            ['reason' => 'Documents verified', 'application_number' => 'ADM-100'],
        );

        $this->assertSame($tenant->id, $log->tenant_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame($scope->id, $log->access_scope_id);
        $this->assertSame($portal->id, $log->portal_id);
        $this->assertSame($role->id, $log->role_id);
        $this->assertTrue($log->subject->is($person));
        $this->assertSame(AuditOutcome::Success, $log->outcome);
        $this->assertSame('Documents verified', $log->reason);
        $this->assertNotNull($log->occurred_at);
        $this->assertNotNull($log->integrity_hash);
    }

    public function test_change_logging_records_only_real_safe_changes_and_normalized_rows(): void
    {
        $tenant = Tenant::factory()->create();
        $person = Person::factory()->for($tenant)->create();
        $this->seed(AuditFoundationSeeder::class);
        $logger = app(AuditLogger::class)->withContext(new AuditContext(tenantId: $tenant->id));

        $log = $logger->change('examination.marks.corrected', $person, [
            'marks' => 64, 'status' => 'verified', 'email' => 'aman@example.com',
            'password' => 'old-secret', 'updated_at' => 'old',
        ], [
            'marks' => 68, 'status' => 'verified', 'email' => 'new@example.com',
            'password' => 'new-secret', 'updated_at' => 'new',
        ], ['reason' => 'Totaling correction']);

        $this->assertSame(['marks', 'email'], $log->changed_fields_json);
        $this->assertSame(64, $log->old_values_json['marks']);
        $this->assertStringContainsString('***@example.com', $log->new_values_json['email']);
        $this->assertArrayNotHasKey('password', $log->new_values_json);
        $this->assertArrayNotHasKey('updated_at', $log->new_values_json);
        $this->assertCount(2, $log->changes);
        $this->assertTrue($log->changes->firstWhere('field_name', 'email')->is_masked);
    }

    public function test_nested_metadata_never_persists_tokens_or_passwords(): void
    {
        $this->seed(AuditFoundationSeeder::class);

        $log = app(AuditLogger::class)
            ->withContext(new AuditContext(source: AuditSource::Api))
            ->success('auth.login.succeeded', null, [
                'identifier' => 'user@example.com',
                'request' => ['api_token' => 'plain-token', 'password' => 'plain-password', 'device' => 'mobile'],
            ]);
        $encoded = json_encode($log->metadata_json, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('plain-token', $encoded);
        $this->assertStringNotContainsString('plain-password', $encoded);
        $this->assertSame('mobile', $log->metadata_json['request']['device']);
    }

    public function test_cross_tenant_subject_is_rejected(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $personB = Person::factory()->for($tenantB)->create();
        $this->seed(AuditFoundationSeeder::class);

        $this->expectException(LogicException::class);

        app(AuditLogger::class)
            ->withContext(new AuditContext(tenantId: $tenantA->id))
            ->success('admissions.application.approved', $personB);
    }

    public function test_audit_logs_and_changes_are_append_only(): void
    {
        $this->seed(AuditFoundationSeeder::class);
        $log = app(AuditLogger::class)
            ->withContext(new AuditContext)
            ->success('auth.login.succeeded');

        $this->expectException(LogicException::class);

        $log->update(['event_title' => 'Rewritten history']);
    }

    public function test_hash_chain_links_consecutive_tenant_events(): void
    {
        $tenant = Tenant::factory()->create();
        $this->seed(AuditFoundationSeeder::class);
        $logger = app(AuditLogger::class)->withContext(new AuditContext(tenantId: $tenant->id));
        $first = $logger->success('auth.login.succeeded');
        $second = $logger->denied('security.cross_tenant.denied');

        $this->assertSame($first->integrity_hash, $second->previous_hash);
        $this->assertNotSame($first->integrity_hash, $second->integrity_hash);
    }

    public function test_required_event_definition_is_protected_and_seeding_is_idempotent(): void
    {
        $this->seed(AuditFoundationSeeder::class);
        $this->seed(AuditFoundationSeeder::class);
        $definition = AuditEventDefinition::query()->where('event_code', 'fees.receipt.cancelled')->firstOrFail();
        $this->assertSame(11, AuditEventDefinition::query()->count());

        $this->expectException(LogicException::class);
        $definition->delete();
    }
}
