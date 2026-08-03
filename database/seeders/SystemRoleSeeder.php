<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Authorization\Permission;
use App\Core\Authorization\Role;
use App\Core\Authorization\RoleType;
use Illuminate\Database\Seeder;

class SystemRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'site-administrator' => ['Site Administrator', RoleType::Platform],
            'tenant-administrator' => ['Tenant Administrator', RoleType::Tenant],
            'management' => ['Management', RoleType::Company],
            'principal' => ['Principal', RoleType::Institute],
            'vice-principal' => ['Vice Principal', RoleType::Institute],
            'teacher' => ['Teacher', RoleType::Staff],
            'class-in-charge' => ['Class In-charge', RoleType::Staff],
            'transport-in-charge' => ['Transport In-charge', RoleType::Staff],
            'accountant' => ['Accountant', RoleType::Staff],
            'fee-clerk' => ['Fee Clerk', RoleType::Staff],
            'parent' => ['Parent', RoleType::Guardian],
            'student' => ['Student', RoleType::Student],
            'alumni' => ['Alumni', RoleType::Alumni],
            'audit-reviewer' => ['Audit Reviewer', RoleType::Staff],
        ];

        foreach ($roles as $code => [$name, $type]) {
            Role::query()->updateOrCreate(
                ['tenant_id' => null, 'code' => $code, 'guard_name' => 'web'],
                ['name' => $name, 'role_type' => $type, 'is_system' => true, 'is_editable' => false, 'is_assignable' => true, 'status' => 'active'],
            );
        }

        $grants = [
            'audit-reviewer' => ['audit.logs.view', 'audit.security.view', 'audit.retention.view', 'audit.alerts.view', 'audit.integrity.view', 'audit.archives.view', 'audit.impersonation.view'],
            'transport-in-charge' => ['transport.access', 'transport.dashboard.view', 'transport.vehicles.view', 'transport.vehicles.create', 'transport.vehicles.update', 'transport.vehicles.retire', 'transport.routes.view', 'transport.routes.create', 'transport.routes.update', 'transport.routes.approve', 'transport.students.view', 'transport.students.assign', 'transport.students.remove', 'transport.reports.view', 'transport.reports.export'],
            'principal' => ['transport.access', 'transport.dashboard.view', 'transport.routes.view', 'transport.routes.approve', 'transport.reports.view'],
            'parent' => ['transport.access', 'transport.child.view', 'transport.tracking.view', 'transport.complaints.create'],
            'student' => ['transport.access', 'transport.own.view', 'transport.tracking.view'],
        ];

        foreach ($grants as $roleCode => $permissionCodes) {
            $role = Role::query()->whereNull('tenant_id')->where('code', $roleCode)->firstOrFail();
            $permissions = Permission::query()->whereIn('code', $permissionCodes)->get();

            foreach ($permissions as $permission) {
                $role->grantPermission($permission);
            }
        }
    }
}
