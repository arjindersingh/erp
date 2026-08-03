<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Authorization\AccessScope;
use App\Core\Authorization\Exceptions\InvalidAccessScope;
use App\Core\Authorization\Exceptions\InvalidRoleAssignment;
use App\Core\Authorization\RoleAssignment;
use App\Core\Authorization\RoleAssignmentStatus;
use App\Core\Authorization\ScopeAccessService;
use App\Core\Authorization\ScopeType;
use App\Core\Identity\IdentityStatus;
use App\Core\Identity\MembershipStatus;
use App\Core\Identity\MembershipType;
use App\Core\Identity\Person;
use App\Core\Identity\Profile;
use App\Core\Identity\ProfileType;
use App\Core\Identity\UserMembership;
use App\Core\Identity\UserPersonLink;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MembershipScopeFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_hierarchy_applies_expected_organizational_inheritance(): void
    {
        $hierarchy = $this->createHierarchy();
        $otherCompany = Company::factory()->for($hierarchy['tenant'])->create();
        $otherCampus = Campus::factory()->for($otherCompany)->create();
        $otherInstitute = Institute::factory()->for($otherCampus)->create();

        $this->assertTrue($hierarchy['tenantScope']->containsScope($hierarchy['instituteScope']));
        $this->assertTrue($hierarchy['companyScope']->containsScope($hierarchy['campusScope']));
        $this->assertTrue($hierarchy['campusScope']->containsScope($hierarchy['instituteScope']));
        $this->assertTrue($hierarchy['tenantScope']->canAccessInstitute($otherInstitute));
        $this->assertFalse($hierarchy['companyScope']->canAccessCampus($otherCampus));
        $this->assertFalse($hierarchy['campusScope']->canAccessInstitute($otherInstitute));
        $this->assertTrue($hierarchy['instituteScope']->canAccessInstitute($hierarchy['institute']));
        $this->assertFalse($hierarchy['instituteScope']->canAccessCampus($hierarchy['campus']));
        $this->assertSame(3, $hierarchy['instituteScope']->level);
        $this->assertStringStartsWith($hierarchy['tenantScope']->path.'/', $hierarchy['companyScope']->path);
    }

    public function test_scope_rejects_invalid_organization_identifiers(): void
    {
        $tenant = Tenant::factory()->create();
        $company = Company::factory()->for($tenant)->create();

        $this->expectException(InvalidAccessScope::class);

        AccessScope::factory()->for($tenant)->create([
            'scope_type' => ScopeType::Tenant,
            'company_id' => $company->id,
        ]);
    }

    public function test_scope_rejects_parent_from_another_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $companyA = Company::factory()->for($tenantA)->create();
        $tenantScopeB = AccessScope::factory()->for($tenantB)->create();

        $this->expectException(InvalidAccessScope::class);

        AccessScope::factory()->forCompany($companyA, $tenantScopeB)->create();
    }

    public function test_user_can_hold_multiple_selectable_memberships_at_different_scopes(): void
    {
        $hierarchy = $this->createHierarchy();
        $secondCampus = Campus::factory()->for($hierarchy['company'])->create();
        $secondInstitute = Institute::factory()->for($secondCampus)->create();
        $secondCampusScope = AccessScope::factory()->forCampus($secondCampus, $hierarchy['companyScope'])->create();
        $secondInstituteScope = AccessScope::factory()->forInstitute($secondInstitute, $secondCampusScope)->create();
        [$user, $person, $profile] = $this->createTeacherIdentity($hierarchy['tenant']);

        $first = $this->createMembership($user, $person, $profile, $hierarchy['instituteScope']);
        $second = $this->createMembership($user, $person, $profile, $secondInstituteScope);

        $this->assertNotSame($first->id, $second->id);
        $this->assertSame(2, $user->memberships()->selectable()->count());
        $this->assertSame(2, $user->activeMemberships()->count());
    }

    public function test_duplicate_active_membership_is_rejected_by_database_constraint(): void
    {
        $hierarchy = $this->createHierarchy();
        [$user, $person, $profile] = $this->createTeacherIdentity($hierarchy['tenant']);
        $this->createMembership($user, $person, $profile, $hierarchy['instituteScope']);

        $this->expectException(QueryException::class);

        $this->createMembership($user, $person, $profile, $hierarchy['instituteScope']);
    }

    public function test_future_expired_and_suspended_memberships_are_not_selectable(): void
    {
        $hierarchy = $this->createHierarchy();
        [$user, $person, $profile] = $this->createTeacherIdentity($hierarchy['tenant']);

        $future = $this->createMembership($user, $person, $profile, $hierarchy['instituteScope'], [
            'starts_at' => now()->addDay(),
        ]);
        $this->assertFalse($future->isSelectableAt());
        $future->update(['status' => MembershipStatus::Inactive]);

        $expired = $this->createMembership($user, $person, $profile, $hierarchy['instituteScope'], [
            'ends_at' => now()->subMinute(),
        ]);
        $this->assertFalse($expired->isSelectableAt());
        $expired->update(['status' => MembershipStatus::Expired]);

        $suspended = $this->createMembership($user, $person, $profile, $hierarchy['instituteScope']);
        $suspended->update(['status' => MembershipStatus::Suspended]);

        $this->assertFalse($future->fresh()->isSelectableAt());
        $this->assertFalse($expired->fresh()->isSelectableAt());
        $this->assertFalse($suspended->fresh()->isSelectableAt());
        $this->assertSame(0, $user->memberships()->selectable()->count());
    }

    public function test_role_assignment_factory_builds_a_consistent_membership_scope(): void
    {
        $assignment = RoleAssignment::factory()->create();

        $this->assertSame($assignment->tenant_id, $assignment->membership->tenant_id);
        $this->assertSame($assignment->user_id, $assignment->membership->user_id);
        $this->assertSame($assignment->access_scope_id, $assignment->membership->access_scope_id);
        $this->assertTrue($assignment->isActiveAt());
    }

    public function test_role_assignment_may_use_membership_scope_or_descendant_only(): void
    {
        $hierarchy = $this->createHierarchy();
        [$user, $person, $profile] = $this->createTeacherIdentity($hierarchy['tenant']);
        $membership = $this->createMembership($user, $person, $profile, $hierarchy['companyScope']);
        $role = Role::create(['name' => 'scope-test-role', 'guard_name' => 'web']);

        $assignment = RoleAssignment::create([
            'tenant_id' => $hierarchy['tenant']->id,
            'user_id' => $user->id,
            'user_membership_id' => $membership->id,
            'role_id' => $role->id,
            'access_scope_id' => $hierarchy['instituteScope']->id,
        ]);

        $this->assertTrue($assignment->isActiveAt());

        $otherHierarchy = $this->createHierarchy();

        $this->expectException(InvalidRoleAssignment::class);

        RoleAssignment::create([
            'tenant_id' => $hierarchy['tenant']->id,
            'user_id' => $user->id,
            'user_membership_id' => $membership->id,
            'role_id' => $role->id,
            'access_scope_id' => $otherHierarchy['instituteScope']->id,
        ]);
    }

    public function test_scope_access_service_uses_selectable_memberships_not_browser_ids(): void
    {
        $hierarchy = $this->createHierarchy();
        [$user, $person, $profile] = $this->createTeacherIdentity($hierarchy['tenant']);
        $membership = $this->createMembership($user, $person, $profile, $hierarchy['companyScope']);
        $service = app(ScopeAccessService::class);

        $this->assertTrue($service->canAccessInstitute($user, $hierarchy['institute']));
        $this->assertTrue($service->canAccessScope($user, $hierarchy['campusScope']));

        $membership->update(['status' => MembershipStatus::Suspended]);

        $this->assertFalse($service->canAccessInstitute($user, $hierarchy['institute']));
        $this->assertFalse($service->canAccessScope($user, $hierarchy['campusScope']));
    }

    public function test_revocation_transitions_are_not_blocked_by_now_inactive_associations(): void
    {
        $hierarchy = $this->createHierarchy();
        [$user, $person, $profile] = $this->createTeacherIdentity($hierarchy['tenant']);
        $membership = $this->createMembership($user, $person, $profile, $hierarchy['instituteScope']);
        $role = Role::create(['name' => 'revocation-test-role', 'guard_name' => 'web']);
        $assignment = RoleAssignment::create([
            'tenant_id' => $hierarchy['tenant']->id,
            'user_id' => $user->id,
            'user_membership_id' => $membership->id,
            'role_id' => $role->id,
            'access_scope_id' => $hierarchy['instituteScope']->id,
        ]);

        $hierarchy['instituteScope']->update(['status' => IdentityStatus::Inactive]);
        $user->personLinks()->update(['status' => IdentityStatus::Revoked]);
        $membership->update(['status' => MembershipStatus::Terminated]);
        $assignment->update(['status' => RoleAssignmentStatus::Terminated]);

        $this->assertSame(MembershipStatus::Terminated, $membership->fresh()->status);
        $this->assertSame(RoleAssignmentStatus::Terminated, $assignment->fresh()->status);
        $this->assertNull($membership->fresh()->active_identity_key);
        $this->assertNull($assignment->fresh()->active_identity_key);
    }

    /**
     * @return array{
     *   tenant: Tenant,
     *   company: Company,
     *   campus: Campus,
     *   institute: Institute,
     *   tenantScope: AccessScope,
     *   companyScope: AccessScope,
     *   campusScope: AccessScope,
     *   instituteScope: AccessScope
     * }
     */
    private function createHierarchy(): array
    {
        $tenant = Tenant::factory()->create();
        $company = Company::factory()->for($tenant)->create();
        $campus = Campus::factory()->for($company)->create();
        $institute = Institute::factory()->for($campus)->create();
        $tenantScope = AccessScope::factory()->for($tenant)->create([
            'name' => $tenant->name,
            'code' => 'TENANT-'.$tenant->id,
        ]);
        $companyScope = AccessScope::factory()->forCompany($company, $tenantScope)->create();
        $campusScope = AccessScope::factory()->forCampus($campus, $companyScope)->create();
        $instituteScope = AccessScope::factory()->forInstitute($institute, $campusScope)->create();

        return compact(
            'tenant',
            'company',
            'campus',
            'institute',
            'tenantScope',
            'companyScope',
            'campusScope',
            'instituteScope',
        );
    }

    /** @return array{User, Person, Profile} */
    private function createTeacherIdentity(Tenant $tenant): array
    {
        $user = User::factory()->create();
        $person = Person::factory()->for($tenant)->create();
        $profile = Profile::factory()->for($person)->create(['type' => ProfileType::Teacher]);
        UserPersonLink::factory()->for($user)->for($person)->create();

        return [$user, $person, $profile];
    }

    /** @param array<string, mixed> $attributes */
    private function createMembership(
        User $user,
        Person $person,
        Profile $profile,
        AccessScope $scope,
        array $attributes = [],
    ): UserMembership {
        return UserMembership::create([
            'tenant_id' => $scope->tenant_id,
            'user_id' => $user->id,
            'person_id' => $person->id,
            'profile_id' => $profile->id,
            'access_scope_id' => $scope->id,
            'membership_type' => MembershipType::Teacher,
            'status' => MembershipStatus::Active,
            ...$attributes,
        ]);
    }
}
