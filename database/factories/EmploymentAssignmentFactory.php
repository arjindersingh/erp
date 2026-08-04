<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Authorization\AccessScope;
use App\Core\Identity\Person;
use App\Domains\Workforce\Models\Department;
use App\Domains\Workforce\Models\Designation;
use App\Domains\Workforce\Models\EmployeeProfile;
use App\Domains\Workforce\Models\EmploymentAssignment;
use App\Domains\Workforce\Models\EmploymentStatus;
use App\Domains\Workforce\Models\EmploymentType;
use App\Domains\Workforce\Models\JobCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EmploymentAssignment> */
final class EmploymentAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return ['department_id' => Department::factory(), 'tenant_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->tenant_id, 'company_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->company_id, 'campus_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->campus_id, 'institute_id' => fn (array $a) => Department::withoutGlobalScopes()->findOrFail($a['department_id'])->institute_id, 'employee_profile_id' => fn (array $a) => EmployeeProfile::factory()->create(['tenant_id' => $a['tenant_id'], 'person_id' => Person::factory()->create(['tenant_id' => $a['tenant_id']])->id])->id, 'job_post_id' => null, 'designation_id' => fn (array $a) => Designation::factory()->create(['tenant_id' => $a['tenant_id']])->id, 'job_category_id' => JobCategory::factory(), 'employment_type_id' => EmploymentType::factory(), 'employment_status_id' => EmploymentStatus::factory(), 'access_scope_id' => fn (array $a) => AccessScope::factory()->create(['tenant_id' => $a['tenant_id']])->id, 'starts_on' => now()->toDateString(), 'is_primary' => false, 'is_additional_posting' => false, 'is_acting_assignment' => false, 'workload_percentage' => 100, 'status' => 'active'];
    }

    public function primary(): static
    {
        return $this->state(['is_primary' => true]);
    }
}
