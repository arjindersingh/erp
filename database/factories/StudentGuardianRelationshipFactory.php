<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\Person;
use App\Domains\Students\Models\GuardianProfile;
use App\Domains\Students\Models\GuardianRelationshipType;
use App\Domains\Students\Models\StudentGuardianRelationship;
use App\Domains\Students\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StudentGuardianRelationship> */
final class StudentGuardianRelationshipFactory extends Factory
{
    public function definition(): array
    {
        return ['student_profile_id' => StudentProfile::factory(), 'tenant_id' => fn (array $a) => StudentProfile::withoutGlobalScopes()->findOrFail($a['student_profile_id'])->tenant_id, 'guardian_profile_id' => fn (array $a) => GuardianProfile::factory()->create(['tenant_id' => $a['tenant_id'], 'person_id' => Person::factory()->create(['tenant_id' => $a['tenant_id']])->id])->id, 'guardian_relationship_type_id' => GuardianRelationshipType::factory(), 'is_primary_guardian' => false, 'is_legal_guardian' => false, 'is_financial_guardian' => false, 'is_academic_contact' => true, 'is_emergency_contact' => true, 'is_pickup_authorised' => false, 'is_medical_consent_authority' => false, 'is_portal_contact' => false, 'is_residential_guardian' => false, 'priority' => 100, 'starts_on' => now()->toDateString(), 'status' => 'active'];
    }

    public function primary(): static
    {
        return $this->state(['is_primary_guardian' => true]);
    }
}
