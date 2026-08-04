<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Core\Identity\Person;
use App\Domains\Students\Models\StudentCategory;
use App\Domains\Students\Models\StudentProfile;
use App\Domains\Students\Models\StudentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StudentProfile> */
final class StudentProfileFactory extends Factory
{
    public function definition(): array
    {
        return ['person_id' => Person::factory(), 'tenant_id' => fn (array $a) => Person::withoutGlobalScopes()->findOrFail($a['person_id'])->tenant_id, 'student_number' => strtoupper(fake()->unique()->bothify('STU-####')), 'registration_date' => now()->toDateString(), 'student_category_id' => StudentCategory::factory(), 'student_status_id' => StudentStatus::factory(), 'student_type' => 'school_student', 'portal_access_allowed' => false, 'communication_allowed' => true, 'status' => 'active'];
    }
}
