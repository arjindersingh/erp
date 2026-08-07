<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Authorization\AccessScope;
use App\Core\Authorization\Permission;
use App\Core\Authorization\Role;
use App\Core\Authorization\RoleAssignment;
use App\Core\Authorization\RoleType;
use App\Core\Authorization\ScopeType;
use App\Core\Identity\AccountStatus;
use App\Core\Identity\AccountType;
use App\Core\Identity\MembershipStatus;
use App\Core\Identity\MembershipType;
use App\Core\Identity\UserMembership;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

final class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['username' => 'System'],
            [
                'name' => 'System',
                'email' => 'system@erp.local',
                'account_type' => AccountType::SiteAdmin,
                'status' => AccountStatus::Active,
                'password' => 'Administrator',
                'email_verified_at' => now(),
                'must_change_password' => false,
            ],
        );

        $role = Role::query()->updateOrCreate(
            ['tenant_id' => null, 'code' => 'site-administrator', 'guard_name' => 'web'],
            [
                'name' => 'Site Administrator',
                'role_type' => RoleType::Platform,
                'is_system' => true,
                'is_editable' => false,
                'is_assignable' => true,
                'status' => 'active',
            ],
        );

        Permission::query()->each(fn (Permission $permission) => $role->grantPermission($permission));

        Tenant::query()->each(function (Tenant $tenant) use ($role, $user): void {
            $scope = AccessScope::withoutGlobalScopes()->firstOrCreate(
                ['tenant_id' => $tenant->id, 'scope_type' => ScopeType::Tenant],
                ['name' => $tenant->name, 'code' => $tenant->code.'-SYSTEM-SCOPE', 'status' => 'active'],
            );

            $membership = UserMembership::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'access_scope_id' => $scope->id,
                    'membership_type' => MembershipType::SiteAdministration,
                ],
                [
                    'is_primary' => true,
                    'status' => MembershipStatus::Active,
                    'approved_at' => now(),
                    'metadata' => ['portal_codes' => [
                        'site_admin', 'management', 'administration', 'staff',
                        'teacher', 'student', 'parent', 'alumni',
                    ]],
                ],
            );

            RoleAssignment::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'user_membership_id' => $membership->id,
                    'role_id' => $role->id,
                    'access_scope_id' => $scope->id,
                ],
                ['is_primary' => true, 'status' => 'active', 'approved_at' => now()],
            );
        });

        Cache::forget('panel-access:'.$user->getKey().':administration');
    }
}
