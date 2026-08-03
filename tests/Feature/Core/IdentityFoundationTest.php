<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Authorization\AccessScope;
use App\Core\Authorization\RoleAssignment;
use App\Core\Identity\AccountStatus;
use App\Core\Identity\AccountType;
use App\Core\Identity\ContactType;
use App\Core\Identity\Exceptions\InvalidMembership;
use App\Core\Identity\MembershipStatus;
use App\Core\Identity\MembershipType;
use App\Core\Identity\Person;
use App\Core\Identity\PersonContact;
use App\Core\Identity\Profile;
use App\Core\Identity\ProfileType;
use App\Core\Identity\UserMembership;
use App\Core\Identity\UserPersonLink;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IdentityFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_account_uses_uuid_enums_and_hashed_passwords(): void
    {
        $user = User::factory()->create([
            'password' => 'SecurePassword!42',
        ]);

        $this->assertNotEmpty($user->uuid);
        $this->assertSame('uuid', $user->getRouteKeyName());
        $this->assertSame(AccountType::Person, $user->account_type);
        $this->assertSame(AccountStatus::Active, $user->status);
        $this->assertTrue($user->isActive());
        $this->assertTrue(Hash::check('SecurePassword!42', $user->password));
        $this->assertNotSame('SecurePassword!42', $user->password);
    }

    public function test_one_global_user_can_link_to_tenant_owned_people_and_multiple_profiles(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = User::factory()->create();
        $personA = Person::factory()->for($tenantA)->create();
        $personB = Person::factory()->for($tenantB)->create();

        UserPersonLink::factory()->for($user)->for($personA)->create(['is_primary' => true]);
        UserPersonLink::factory()->for($user)->for($personB)->create();

        Profile::factory()->for($personA)->create(['type' => ProfileType::Teacher]);
        Profile::factory()->for($personA)->create(['type' => ProfileType::Guardian]);
        Profile::factory()->for($personB)->create(['type' => ProfileType::Alumni]);

        $this->assertCount(2, $user->persons);
        $this->assertCount(3, $user->profiles);
        $this->assertTrue($personA->teacherProfile()->exists());
        $this->assertTrue($personA->guardianProfile()->exists());
        $this->assertSame($tenantA->id, $personA->tenant_id);
        $this->assertSame($tenantB->id, $personB->tenant_id);
    }

    public function test_person_supports_multiple_email_mobile_and_whatsapp_contacts(): void
    {
        $person = Person::factory()->create();

        PersonContact::factory()->for($person)->create([
            'type' => ContactType::Email,
            'value' => 'PERSONAL@example.test',
            'normalized_value' => 'personal@example.test',
            'is_primary' => true,
        ]);
        PersonContact::factory()->for($person)->create([
            'type' => ContactType::Mobile,
            'value' => '+91 99999 11111',
            'normalized_value' => '+919999911111',
        ]);
        PersonContact::factory()->for($person)->create([
            'type' => ContactType::WhatsApp,
            'value' => '+91 99999 22222',
            'normalized_value' => '+919999922222',
        ]);

        $this->assertCount(3, $person->contacts);
        $this->assertTrue($person->contacts->contains('type', ContactType::Email));
        $this->assertTrue($person->contacts->contains('type', ContactType::Mobile));
        $this->assertTrue($person->contacts->contains('type', ContactType::WhatsApp));
    }

    public function test_membership_connects_identity_scope_and_role_without_conflating_them(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $person = Person::factory()->for($tenant)->create();
        $profile = Profile::factory()->for($person)->create(['type' => ProfileType::Teacher]);
        $scope = AccessScope::factory()->for($tenant)->create();
        UserPersonLink::factory()->for($user)->for($person)->create();

        $membership = UserMembership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'person_id' => $person->id,
            'profile_id' => $profile->id,
            'access_scope_id' => $scope->id,
            'membership_type' => MembershipType::Teacher,
            'status' => MembershipStatus::Active,
            'is_primary' => true,
        ]);
        $role = Role::create(['name' => 'class-teacher', 'guard_name' => 'web']);
        $assignment = RoleAssignment::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'user_membership_id' => $membership->id,
            'role_id' => $role->id,
            'access_scope_id' => $scope->id,
        ]);

        $this->assertTrue($membership->isActiveAt());
        $this->assertTrue($assignment->isActiveAt());
        $this->assertTrue($membership->accessScope()->firstOrFail()->is($scope));
        $this->assertTrue($membership->profile()->firstOrFail()->is($profile));
        $this->assertTrue($assignment->role()->firstOrFail()->is($role));
        $this->assertTrue($user->roleAssignments()->whereKey($assignment)->exists());
        $this->assertTrue($tenant->users()->whereKey($user)->exists());
    }

    public function test_a_person_cannot_be_linked_to_two_users_within_the_same_tenant(): void
    {
        $person = Person::factory()->create();
        UserPersonLink::factory()->for($person)->create();

        $this->expectException(QueryException::class);

        UserPersonLink::factory()->for($person)->create();
    }

    public function test_database_rejects_cross_tenant_membership_references(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = User::factory()->create();
        $personA = Person::factory()->for($tenantA)->create();
        $profileA = Profile::factory()->for($personA)->create(['type' => ProfileType::Teacher]);
        $scopeB = AccessScope::factory()->for($tenantB)->create();
        UserPersonLink::factory()->for($user)->for($personA)->create();

        $this->expectException(InvalidMembership::class);

        UserMembership::create([
            'tenant_id' => $tenantB->id,
            'user_id' => $user->id,
            'person_id' => $personA->id,
            'profile_id' => $profileA->id,
            'access_scope_id' => $scopeB->id,
            'membership_type' => MembershipType::Teacher,
            'status' => MembershipStatus::Active,
        ]);
    }

    public function test_tenant_scopes_do_not_return_other_tenant_identity_records(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        Person::factory()->count(2)->for($tenantA)->create();
        Person::factory()->count(3)->for($tenantB)->create();

        $this->assertSame(2, Person::query()->forTenant($tenantA)->count());
        $this->assertSame(3, Person::query()->forTenant($tenantB)->count());
        $this->assertSame(2, $tenantA->persons()->count());
        $this->assertSame(3, $tenantB->persons()->count());
    }
}
