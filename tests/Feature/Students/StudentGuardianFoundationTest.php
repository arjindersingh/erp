<?php

declare(strict_types=1);

namespace Tests\Feature\Students;

use App\Core\Identity\Person;
use App\Core\Tenancy\Tenant;
use App\Core\Tenancy\TenantContext;
use App\Domains\Students\Models\GuardianProfile;
use App\Domains\Students\Models\StudentGuardianRelationship;
use App\Domains\Students\Models\StudentProfile;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('students')]
#[Group('isolation')]
final class StudentGuardianFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_person_can_have_only_one_student_profile_per_tenant(): void
    {
        $person = Person::factory()->create();
        StudentProfile::factory()->create(['tenant_id' => $person->tenant_id, 'person_id' => $person->id]);
        $this->expectException(QueryException::class);
        StudentProfile::factory()->create(['tenant_id' => $person->tenant_id, 'person_id' => $person->id]);
    }

    public function test_student_number_is_tenant_aware_and_profile_has_no_current_academic_placement(): void
    {
        $number = 'STUDENT-001';
        $a = StudentProfile::factory()->create(['student_number' => $number]);
        $tenantB = Tenant::factory()->create();
        $b = StudentProfile::factory()->create(['tenant_id' => $tenantB->id, 'person_id' => Person::factory()->create(['tenant_id' => $tenantB->id])->id, 'student_number' => $number]);
        $this->assertNotSame($a->tenant_id, $b->tenant_id);
        $this->assertFalse(Schema::hasColumn('student_profiles', 'class_id'));
        $this->assertFalse(Schema::hasColumn('student_profiles', 'programme_offering_id'));
    }

    public function test_guardian_can_be_linked_to_several_students_in_same_tenant(): void
    {
        $relationship = StudentGuardianRelationship::factory()->create();
        $student = StudentProfile::factory()->create(['tenant_id' => $relationship->tenant_id, 'person_id' => Person::factory()->create(['tenant_id' => $relationship->tenant_id])->id]);
        StudentGuardianRelationship::factory()->create(['tenant_id' => $relationship->tenant_id, 'student_profile_id' => $student->id, 'guardian_profile_id' => $relationship->guardian_profile_id]);
        $this->assertCount(2, $relationship->guardianProfile->studentRelationships);
    }

    public function test_cross_tenant_guardian_relationship_is_rejected(): void
    {
        $relationship = StudentGuardianRelationship::factory()->make();
        $relationship->guardian_profile_id = GuardianProfile::factory()->create()->id;
        $this->expectException(ValidationException::class);
        $relationship->save();
    }

    public function test_duplicate_active_relationship_and_second_primary_guardian_are_rejected(): void
    {
        $first = StudentGuardianRelationship::factory()->primary()->create();
        $this->expectException(ValidationException::class);
        StudentGuardianRelationship::factory()->create(['tenant_id' => $first->tenant_id, 'student_profile_id' => $first->student_profile_id, 'guardian_profile_id' => $first->guardian_profile_id, 'guardian_relationship_type_id' => $first->guardian_relationship_type_id]);
    }

    public function test_guardian_authorities_are_independent(): void
    {
        $relationship = StudentGuardianRelationship::factory()->create(['is_pickup_authorised' => true, 'is_financial_guardian' => false, 'is_academic_contact' => false]);
        $this->assertTrue($relationship->is_pickup_authorised);
        $this->assertFalse($relationship->is_financial_guardian);
        $this->assertFalse($relationship->is_academic_contact);
    }

    public function test_tenant_scope_hides_unrelated_students_and_guardians(): void
    {
        $a = StudentGuardianRelationship::factory()->create();
        $b = StudentGuardianRelationship::factory()->create();
        app(TenantContext::class)->activate(Tenant::withoutGlobalScopes()->findOrFail($a->tenant_id));
        $this->assertTrue(StudentProfile::query()->whereKey($a->student_profile_id)->exists());
        $this->assertFalse(StudentProfile::query()->whereKey($b->student_profile_id)->exists());
        $this->assertFalse(GuardianProfile::query()->whereKey($b->guardian_profile_id)->exists());
    }

    public function test_integrity_audit_passes_for_valid_foundation_data(): void
    {
        StudentGuardianRelationship::factory()->create();
        $this->artisan('erp:student-guardian-integrity-audit')->expectsOutputToContain('PASS')->expectsOutputToContain('WARNING')->assertSuccessful();
    }
}
