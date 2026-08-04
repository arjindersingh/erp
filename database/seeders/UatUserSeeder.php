<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Authorization\AccessScope;
use App\Core\Authorization\Permission;
use App\Core\Authorization\Role;
use App\Core\Authorization\RoleAssignment;
use App\Core\Identity\Person;
use App\Core\Identity\Profile;
use App\Core\Identity\UserMembership;
use App\Core\Identity\UserPersonLink;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class UatUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('UAT_TEMP_PASSWORD') ?: Str::password(20);
        foreach (Tenant::query()->whereIn('code', ['UAT-A', 'UAT-B'])->get() as $tenant) {
            $scope = AccessScope::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('scope_type', 'institute')->orderBy('id')->firstOrFail();
            $email = 'admissions.'.strtolower($tenant->code).'@erp-uat.test';
            $user = User::query()->updateOrCreate(['email' => $email], ['name' => 'Reena Sharma', 'password' => $password, 'status' => 'active', 'email_verified_at' => now()]);
            $person = Person::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'primary_email' => $email], ['first_name' => 'Reena', 'last_name' => 'Sharma', 'display_name' => 'Reena Sharma', 'status' => 'active']);
            UserPersonLink::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'user_id' => $user->id], ['person_id' => $person->id, 'is_primary' => true, 'status' => 'active']);
            $profile = Profile::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'person_id' => $person->id, 'type' => 'employee'], ['display_name' => 'Admission In-charge', 'reference_number' => $tenant->code.'-EMP-0010', 'is_primary' => true, 'status' => 'active']);
            $membership = UserMembership::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'access_scope_id' => $scope->id, 'membership_type' => 'employee'], ['person_id' => $person->id, 'profile_id' => $profile->id, 'is_primary' => true, 'status' => 'active', 'approved_at' => now(), 'metadata' => ['portal_codes' => ['administration']]]);
            $role = Role::query()->updateOrCreate(['tenant_id' => $tenant->id, 'code' => 'admission-in-charge', 'guard_name' => 'web'], ['name' => 'Admission In-charge '.$tenant->code, 'role_type' => 'staff', 'status' => 'active']);
            foreach (['admissions.access', 'admissions.dashboard.view', 'admissions.applications.view', 'admissions.reports.view', 'access.diagnostics.use'] as $code) {
                $permission = Permission::query()->where('code', $code)->firstOrFail();
                $role->grantPermission($permission);
            }
            RoleAssignment::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'user_membership_id' => $membership->id, 'role_id' => $role->id, 'access_scope_id' => $scope->id], ['is_primary' => true, 'status' => 'active']);
            $this->command?->warn("UAT login {$email} temporary password: {$password}");
        }
    }
}
