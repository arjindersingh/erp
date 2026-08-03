<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Authorization\Exceptions\InvalidPermissionCode;
use App\Core\Authorization\Permission;
use App\Core\Authorization\Role;
use App\Core\Authorization\RolePermission;
use App\Core\Modules\Module;
use App\Core\Tenancy\Tenant;
use Database\Seeders\CoreModuleSeeder;
use Database\Seeders\CorePermissionSeeder;
use Database\Seeders\SystemRoleSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class DynamicAccessFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_codes_follow_the_command_naming_standard(): void
    {
        $module = Module::factory()->create(['code' => 'students']);

        $valid = Permission::factory()->for($module)->create([
            'name' => 'students.profile.update',
            'code' => 'students.profile.update',
        ]);

        $this->assertSame('students.profile.update', $valid->name);

        $this->expectException(InvalidPermissionCode::class);

        Permission::factory()->for($module)->create(['name' => 'manage_students', 'code' => 'manage_students']);
    }

    public function test_duplicate_permission_code_is_rejected_per_guard(): void
    {
        $module = Module::factory()->create(['code' => 'fees']);
        Permission::factory()->for($module)->create(['name' => 'fees.receipts.view', 'code' => 'fees.receipts.view']);

        $this->expectException(QueryException::class);

        Permission::factory()->for($module)->create(['name' => 'fees.receipts.view', 'code' => 'fees.receipts.view']);
    }

    public function test_tenant_roles_are_isolated_and_names_may_repeat(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $roleA = Role::factory()->for($tenantA)->create(['name' => 'Fee Clerk', 'code' => 'fee-clerk']);
        $roleB = Role::factory()->for($tenantB)->create(['name' => 'Fee Clerk', 'code' => 'fee-clerk']);

        $this->assertNotSame($roleA->tenant_id, $roleB->tenant_id);
        $this->assertCount(1, Role::query()->where('tenant_id', $tenantA->id)->get());
    }

    public function test_role_can_hold_multiple_permissions_with_grant_metadata(): void
    {
        $module = Module::factory()->create(['code' => 'transport']);
        $role = Role::factory()->create();
        $view = Permission::factory()->for($module)->create();
        $update = Permission::factory()->for($module)->create([
            'name' => 'transport.routes.update',
            'code' => 'transport.routes.update',
            'command' => 'update',
        ]);

        $role->grantPermission($view);
        $role->grantPermission($update);

        $this->assertCount(2, $role->permissions);
        $this->assertSame(2, RolePermission::query()->where('role_id', $role->id)->count());
    }

    public function test_protected_system_role_cannot_be_deleted(): void
    {
        $role = Role::factory()->create(['is_system' => true, 'is_editable' => false]);

        $this->expectException(LogicException::class);

        $role->delete();
    }

    public function test_seeders_are_idempotent_and_prove_shared_transport_access(): void
    {
        $this->seed(CoreModuleSeeder::class);
        $this->seed(CorePermissionSeeder::class);
        $this->seed(SystemRoleSeeder::class);
        $this->seed(SystemRoleSeeder::class);

        $parent = Role::query()->where('code', 'parent')->firstOrFail();
        $principal = Role::query()->where('code', 'principal')->firstOrFail();
        $transport = Module::query()->where('code', 'transport')->firstOrFail();

        $this->assertTrue($parent->permissions()->where('code', 'transport.child.view')->exists());
        $this->assertFalse($parent->permissions()->where('code', 'transport.routes.approve')->exists());
        $this->assertTrue($principal->permissions()->where('code', 'transport.routes.approve')->exists());
        $this->assertSame(20, $transport->permissions()->count());
    }
}
