<?php

declare(strict_types=1);

namespace Tests\Feature\Academics\IntegrationContracts;

use App\Core\AcademicYear\AcademicYearContext;
use App\Core\Authorization\AccessScope;
use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Core\Tenancy\Tenant;
use App\Domains\Academics\Contracts\AcademicContextProvider;
use App\Domains\Academics\Contracts\ClassSectionProvider;
use App\Domains\Academics\Contracts\ProgrammeSemesterProvider;
use App\Domains\Academics\Contracts\SubjectOfferingProvider;
use App\Domains\Academics\Models\AcademicClass;
use App\Domains\Academics\Models\AcademicCourse;
use App\Domains\Academics\Models\AcademicProgramme;
use App\Domains\Academics\Models\AcademicSection;
use App\Domains\Academics\Models\AcademicStructureVersion;
use App\Domains\Academics\Models\AcademicSubject;
use App\Domains\Academics\Models\AcademicTerm;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Academics\Models\ClassSubjectMapping;
use App\Domains\Academics\Models\EducationLevel;
use App\Domains\Academics\Models\ProgrammeOffering;
use App\Domains\Academics\Models\ProgrammeSubjectMapping;
use App\Domains\Academics\Models\Semester;
use App\Domains\Academics\Models\SemesterOffering;
use App\Domains\Academics\Models\SubjectOffering;
use App\Domains\Academics\Services\AcademicStructureCloneService;
use Database\Seeders\AuditFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('academics')]
#[Group('academic-structure')]
#[Group('integration-contract')]
#[Group('isolation')]
final class CanonicalAcademicSpineTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_and_college_consumers_share_the_canonical_spine(): void
    {
        $data = $this->structure();
        $context = app(AcademicYearContext::class);
        $context->activate($data['year'], $data['school_scope']);

        $this->assertSame(['Class 10'], app(AcademicContextProvider::class)->classesForContext($context)->pluck('name')->all());
        $this->assertSame(['Section A'], app(ClassSectionProvider::class)->sectionsForClass($data['class'], $context)->pluck('name')->all());
        $this->assertSame(['ENG-10-A'], app(SubjectOfferingProvider::class)->forSection($data['section'], $context)->pluck('delivery_key')->all());

        $context->activate($data['year'], $data['college_scope']);
        $this->assertSame(['BTECH-2026'], app(AcademicContextProvider::class)->programmesForContext($context)->pluck('code')->all());
        $this->assertCount(1, app(ProgrammeSemesterProvider::class)->semesterOfferingsForProgramme($data['programme_offering']));
        $this->assertSame(['CS101-BATCH-A'], app(SubjectOfferingProvider::class)->forSemester($data['semester_offering'], $context)->pluck('delivery_key')->all());
    }

    public function test_cross_tenant_relationships_are_rejected(): void
    {
        $data = $this->structure();
        $foreign = Tenant::factory()->create(['name' => 'Tenant B']);

        $this->expectException(ValidationException::class);
        AcademicCourse::query()->create(['tenant_id' => $foreign->id, 'academic_programme_id' => $data['programme']->id, 'code' => 'HOSTILE', 'name' => 'Hostile']);
    }

    public function test_cross_institute_section_context_is_rejected(): void
    {
        $data = $this->structure();

        $this->expectException(ValidationException::class);
        AcademicSection::query()->create(['tenant_id' => $data['tenant']->id, 'institute_id' => $data['college']->id, 'academic_year_id' => $data['year']->id, 'academic_class_id' => $data['class']->id, 'type' => 'section', 'code' => 'HOSTILE', 'name' => 'Hostile']);
    }

    public function test_locked_year_blocks_structural_mutation(): void
    {
        $data = $this->structure();
        $data['year']->update(['status' => 'locked']);

        $this->expectException(ValidationException::class);
        AcademicTerm::query()->create(['tenant_id' => $data['tenant']->id, 'institute_id' => $data['school']->id, 'academic_year_id' => $data['year']->id, 'code' => 'T2', 'name' => 'Term 2', 'starts_on' => '2026-10-01', 'ends_on' => '2027-03-01']);
    }

    public function test_subject_offering_must_match_its_mapping_subject(): void
    {
        $data = $this->structure();
        $other = AcademicSubject::query()->create(['tenant_id' => $data['tenant']->id, 'code' => 'MATH', 'name' => 'Mathematics']);

        $this->expectException(ValidationException::class);
        SubjectOffering::query()->create(['tenant_id' => $data['tenant']->id, 'institute_id' => $data['school']->id, 'academic_year_id' => $data['year']->id, 'academic_subject_id' => $other->id, 'class_subject_mapping_id' => $data['class_mapping']->id, 'academic_section_id' => $data['section']->id, 'academic_term_id' => $data['term']->id, 'delivery_key' => 'INVALID']);
    }

    #[Group('authorization')]
    public function test_structure_clone_generates_new_keys_and_never_copies_transactional_data(): void
    {
        $data = $this->structure();
        $this->seed(AuditFoundationSeeder::class);
        $destination = AcademicYear::factory()->for($data['tenant'])->create(['code' => '2027-28', 'name' => '2027–28', 'starts_on' => '2027-04-01', 'ends_on' => '2028-03-31', 'status' => 'open']);
        $sourceClassId = $data['class']->id;
        $sourceSectionId = $data['section']->id;

        $result = app(AcademicStructureCloneService::class)->clone($data['year'], $destination, $data['school']);

        $this->assertSame(['classes' => 1, 'sections' => 1, 'terms' => 1], $result);
        $this->assertDatabaseHas('academic_classes', ['academic_year_id' => $destination->id, 'code' => '10']);
        $this->assertDatabaseMissing('academic_classes', ['academic_year_id' => $destination->id, 'id' => $sourceClassId]);
        $this->assertDatabaseMissing('academic_sections', ['academic_year_id' => $destination->id, 'id' => $sourceSectionId]);
        $this->assertSame(2, SubjectOffering::withoutGlobalScopes()->count(), 'Transactional subject offerings are not cloned.');
        $this->assertDatabaseHas('audit_logs', ['tenant_id' => $data['tenant']->id, 'event_code' => 'academic.structure.cloned', 'subject_id' => $destination->id]);
    }

    private function structure(): array
    {
        $tenant = Tenant::factory()->create(['name' => 'Tenant A']);
        $company = Company::factory()->for($tenant)->create(['name' => 'Company A']);
        $campus = Campus::factory()->for($company)->create(['name' => 'Campus A']);
        $school = Institute::factory()->for($campus)->create(['name' => 'School A']);
        $college = Institute::factory()->for($campus)->create(['name' => 'College A']);
        $tenantScope = AccessScope::factory()->for($tenant)->create();
        $companyScope = AccessScope::factory()->forCompany($company, $tenantScope)->create();
        $campusScope = AccessScope::factory()->forCampus($campus, $companyScope)->create();
        $schoolScope = AccessScope::factory()->forInstitute($school, $campusScope)->create();
        $collegeScope = AccessScope::factory()->forInstitute($college, $campusScope)->create();
        $year = AcademicYear::factory()->current()->for($tenant)->create(['company_id' => null, 'campus_id' => null, 'institute_id' => null, 'code' => '2026-27', 'name' => '2026–27']);
        $level = EducationLevel::query()->create(['tenant_id' => $tenant->id, 'code' => 'SECONDARY', 'name' => 'Secondary', 'level_category' => 'secondary', 'sequence' => 10]);
        $class = AcademicClass::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $school->id, 'academic_year_id' => $year->id, 'education_level_id' => $level->id, 'code' => '10', 'name' => 'Class 10']);
        $section = AcademicSection::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $school->id, 'academic_year_id' => $year->id, 'academic_class_id' => $class->id, 'type' => 'section', 'code' => 'A', 'name' => 'Section A']);
        $term = AcademicTerm::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $school->id, 'academic_year_id' => $year->id, 'code' => 'T1', 'name' => 'Term 1', 'starts_on' => '2026-04-01', 'ends_on' => '2026-09-30']);
        $english = AcademicSubject::query()->create(['tenant_id' => $tenant->id, 'code' => 'ENG', 'name' => 'English', 'maximum_marks' => 100, 'passing_marks' => 33]);
        $schoolVersion = AcademicStructureVersion::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $school->id, 'academic_year_id' => $year->id, 'version' => '1', 'name' => 'School Curriculum']);
        $classMapping = ClassSubjectMapping::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $school->id, 'academic_year_id' => $year->id, 'academic_class_id' => $class->id, 'academic_subject_id' => $english->id, 'academic_term_id' => $term->id, 'academic_structure_version_id' => $schoolVersion->id, 'classification' => 'core', 'maximum_marks' => 100, 'passing_marks' => 33]);
        SubjectOffering::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $school->id, 'academic_year_id' => $year->id, 'academic_subject_id' => $english->id, 'class_subject_mapping_id' => $classMapping->id, 'academic_section_id' => $section->id, 'academic_term_id' => $term->id, 'delivery_key' => 'ENG-10-A']);
        $programme = AcademicProgramme::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $college->id, 'education_level_id' => $level->id, 'code' => 'BTECH', 'name' => 'B.Tech', 'duration_months' => 48, 'credit_system' => 'credits', 'required_credits' => 160]);
        $course = AcademicCourse::query()->create(['tenant_id' => $tenant->id, 'academic_programme_id' => $programme->id, 'code' => 'CSE', 'name' => 'Computer Science and Engineering']);
        $programmeOffering = ProgrammeOffering::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $college->id, 'academic_year_id' => $year->id, 'academic_programme_id' => $programme->id, 'code' => 'BTECH-2026', 'intake_capacity' => 60]);
        $semester = Semester::query()->create(['tenant_id' => $tenant->id, 'academic_programme_id' => $programme->id, 'number' => 1, 'name' => 'Semester 1']);
        $semesterOffering = SemesterOffering::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $college->id, 'academic_year_id' => $year->id, 'programme_offering_id' => $programmeOffering->id, 'semester_id' => $semester->id, 'starts_on' => '2026-04-01', 'ends_on' => '2026-09-30']);
        $collegeVersion = AcademicStructureVersion::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $college->id, 'academic_year_id' => $year->id, 'version' => '1', 'name' => 'B.Tech Curriculum']);
        $mapping = ProgrammeSubjectMapping::query()->create(['tenant_id' => $tenant->id, 'academic_programme_id' => $programme->id, 'academic_course_id' => $course->id, 'semester_id' => $semester->id, 'academic_subject_id' => $english->id, 'academic_structure_version_id' => $collegeVersion->id, 'classification' => 'core', 'credits' => 4]);
        SubjectOffering::query()->create(['tenant_id' => $tenant->id, 'institute_id' => $college->id, 'academic_year_id' => $year->id, 'academic_subject_id' => $english->id, 'programme_subject_mapping_id' => $mapping->id, 'semester_offering_id' => $semesterOffering->id, 'delivery_key' => 'CS101-BATCH-A']);

        return [
            'tenant' => $tenant, 'school' => $school, 'college' => $college,
            'school_scope' => $schoolScope, 'college_scope' => $collegeScope,
            'year' => $year, 'class' => $class, 'section' => $section, 'term' => $term,
            'class_mapping' => $classMapping, 'programme' => $programme,
            'programme_offering' => $programmeOffering, 'semester_offering' => $semesterOffering,
        ];
    }
}
