<?php

declare(strict_types=1);

namespace Tests\Feature\Workforce;

use App\Core\Identity\Person;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Domains\Workforce\Models\Department;
use App\Domains\Workforce\Models\EmployeeProfile;
use App\Domains\Workforce\Models\EmploymentAssignment;
use App\Domains\Workforce\Models\EmploymentStatus;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('workforce')]
#[Group('isolation')]
final class WorkforceEmploymentFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_person_has_only_one_employee_profile_per_tenant(): void
    {
        $person = Person::factory()->create();
        EmployeeProfile::factory()->create(['tenant_id' => $person->tenant_id, 'person_id' => $person->id]);
        $this->expectException(QueryException::class);
        EmployeeProfile::factory()->create(['tenant_id' => $person->tenant_id, 'person_id' => $person->id]);
    }

    public function test_employee_can_have_several_assignments_and_one_primary(): void
    {
        $first = EmploymentAssignment::factory()->primary()->create();
        $second = EmploymentAssignment::factory()->create([
            'employee_profile_id' => $first->employee_profile_id,
            'tenant_id' => $first->tenant_id,
            'company_id' => $first->company_id,
            'campus_id' => $first->campus_id,
            'institute_id' => $first->institute_id,
            'department_id' => $first->department_id,
            'designation_id' => $first->designation_id,
            'access_scope_id' => $first->access_scope_id,
        ]);
        $first->employeeProfile->refresh();
        $this->assertCount(2, $first->employeeProfile->assignments);
        $this->assertTrue($first->is_primary);
        $this->assertSame($first->id, $first->employeeProfile->primary_employment_assignment_id);
        $this->assertFalse($second->is_primary);
    }

    public function test_cross_tenant_employee_assignment_is_rejected(): void
    {
        $assignment = EmploymentAssignment::factory()->make();
        $assignment->tenant_id = Tenant::factory()->create()->id;
        $this->expectException(ValidationException::class);
        $assignment->save();
    }

    public function test_inactive_employee_cannot_receive_assignment(): void
    {
        $profile = EmployeeProfile::factory()->create(['employment_status_id' => EmploymentStatus::factory()->inactive()]);
        $this->expectException(ValidationException::class);
        EmploymentAssignment::factory()->create(['tenant_id' => $profile->tenant_id, 'employee_profile_id' => $profile->id]);
    }

    public function test_assignment_creation_preserves_history(): void
    {
        $assignment = EmploymentAssignment::factory()->create();
        $this->assertDatabaseHas('employment_assignment_histories', ['tenant_id' => $assignment->tenant_id, 'employment_assignment_id' => $assignment->id, 'action' => 'appointment']);
    }

    public function test_cross_boundary_parent_department_is_rejected(): void
    {
        $parent = Department::factory()->create();
        $child = Department::factory()->make(['parent_id' => $parent->id]);

        $this->expectException(ValidationException::class);
        $child->save();
    }

    public function test_cross_institute_department_assignment_is_rejected(): void
    {
        $assignment = EmploymentAssignment::factory()->make();
        $assignment->department_id = Department::factory()->create()->id;

        $this->expectException(ValidationException::class);
        $assignment->save();
    }

    public function test_foundation_integrity_audit_passes(): void
    {
        EmploymentAssignment::factory()->create();

        $this->artisan('erp:employee-teaching-assignment-audit')
            ->expectsOutputToContain('PASS')
            ->expectsOutputToContain('WARNING')
            ->assertSuccessful();
    }

    public function test_tenant_scope_hides_other_tenants_employee_profiles(): void
    {
        $a = EmployeeProfile::factory()->create();
        $b = EmployeeProfile::factory()->create();
        app(TenantContext::class)->activate(Tenant::withoutGlobalScopes()->findOrFail($a->tenant_id));
        $this->assertTrue(EmployeeProfile::query()->whereKey($a)->exists());
        $this->assertFalse(EmployeeProfile::query()->whereKey($b)->exists());
    }
}
