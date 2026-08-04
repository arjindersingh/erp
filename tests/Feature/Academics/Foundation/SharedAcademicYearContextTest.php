<?php

declare(strict_types=1);

namespace Tests\Feature\Academics\Foundation;

use App\Core\AcademicYear\AcademicYearContext;
use App\Core\AcademicYear\AcademicYearLockService;
use App\Core\AcademicYear\SelectAcademicYearAction;
use App\Core\Authorization\AccessScope;
use App\Core\Identity\UserMembership;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Academics\Models\AcademicYearLock;
use App\Domains\Academics\Models\AcademicYearScopeAssignment;
use App\Domains\Academics\Services\AcademicYearResolver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('academics')]
#[Group('foundation')]
final class SharedAcademicYearContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolver_never_crosses_tenant_without_an_active_tenant_context(): void
    {
        $institute = Institute::factory()->create();
        AcademicYear::factory()->current()->create();

        $this->assertNull(app(AcademicYearResolver::class)->currentForInstitute($institute));
    }

    public function test_user_can_activate_an_authorized_opaque_year_option(): void
    {
        $tenant = Tenant::factory()->create();
        $scope = AccessScope::factory()->for($tenant)->create();
        $user = User::factory()->create();
        UserMembership::factory()->for($user)->for($scope, 'accessScope')->create(['tenant_id' => $tenant->id]);
        $year = AcademicYear::factory()->current()->for($tenant)->create();
        app(TenantContext::class)->activate($tenant);
        $action = app(SelectAcademicYearAction::class);

        $selected = $action->execute($user, $action->optionId($year, $scope));

        $this->assertTrue($selected->is($year));
        $this->assertTrue(app(AcademicYearContext::class)->requireYear()->is($year));
    }

    public function test_tampered_year_option_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();
        app(TenantContext::class)->activate($tenant);

        $this->expectException(ValidationException::class);
        app(SelectAcademicYearAction::class)->execute(User::factory()->create(), 'forged');
    }

    public function test_scope_assignment_rejects_a_foreign_tenant_scope(): void
    {
        $year = AcademicYear::factory()->create();
        $foreignScope = AccessScope::factory()->create();

        $this->expectException(ValidationException::class);
        AcademicYearScopeAssignment::query()->create([
            'tenant_id' => $year->tenant_id, 'academic_year_id' => $year->id,
            'access_scope_id' => $foreignScope->id, 'status' => 'active',
        ]);
    }

    public function test_granular_active_lock_makes_matching_area_read_only(): void
    {
        $tenant = Tenant::factory()->create();
        $scope = AccessScope::factory()->for($tenant)->create();
        $year = AcademicYear::factory()->for($tenant)->create(['status' => 'active']);
        AcademicYearLock::query()->create([
            'tenant_id' => $tenant->id, 'academic_year_id' => $year->id,
            'access_scope_id' => $scope->id, 'module_key' => 'attendance',
            'reason' => 'Term results finalized.', 'status' => 'active',
        ]);

        $locks = app(AcademicYearLockService::class);
        $this->assertFalse($locks->isWritable($year, $scope, 'attendance'));
        $this->assertTrue($locks->isWritable($year, $scope, 'admissions'));
    }

    public function test_expired_lock_does_not_block_writes(): void
    {
        $tenant = Tenant::factory()->create();
        $scope = AccessScope::factory()->for($tenant)->create();
        $year = AcademicYear::factory()->for($tenant)->create(['status' => 'active']);
        AcademicYearLock::query()->create([
            'tenant_id' => $tenant->id, 'academic_year_id' => $year->id,
            'access_scope_id' => $scope->id, 'reason' => 'Temporary close.',
            'status' => 'active', 'ends_at' => now()->subMinute(),
        ]);

        $this->assertTrue(app(AcademicYearLockService::class)->isWritable($year, $scope));
    }
}
