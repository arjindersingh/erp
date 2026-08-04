<?php

declare(strict_types=1);

namespace App\Domains\Academics\Services;

use App\Domains\Academics\Models\AcademicStructureVersion;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Academics\Models\CanonicalAcademicModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AcademicStructureValidator
{
    public function validate(CanonicalAcademicModel $record): void
    {
        $this->nonNegative($record, ['duration_months', 'required_credits', 'intake_capacity', 'capacity', 'credits', 'maximum_marks', 'passing_marks', 'weekly_periods', 'theory_hours', 'practical_hours', 'minimum_selections', 'maximum_selections']);
        if ($record->getAttribute('passing_marks') !== null && $record->getAttribute('maximum_marks') !== null
            && (float) $record->getAttribute('passing_marks') > (float) $record->getAttribute('maximum_marks')) {
            $this->fail('passing_marks', 'Passing marks cannot exceed maximum marks.');
        }
        if ($record->getAttribute('minimum_selections') !== null && $record->getAttribute('maximum_selections') !== null
            && (int) $record->getAttribute('minimum_selections') > (int) $record->getAttribute('maximum_selections')) {
            $this->fail('maximum_selections', 'Maximum selections cannot be less than minimum selections.');
        }
        foreach ([['starts_on', 'ends_on'], ['starts_at', 'ends_at']] as [$start, $end]) {
            if ($record->getAttribute($start) !== null && $record->getAttribute($end) !== null
                && $record->getAttribute($start) >= $record->getAttribute($end)) {
                $this->fail($end, 'End must be after start.');
            }
        }

        $tenantId = (int) $record->getAttribute('tenant_id');
        foreach ($this->references($record->getTable()) as $column => $table) {
            $id = $record->getAttribute($column);
            if ($id !== null && ! DB::table($table)->where('id', $id)->where('tenant_id', $tenantId)->exists()) {
                $this->fail($column, 'Referenced academic record must belong to the same tenant.');
            }
        }
        if ($record->getAttribute('institute_id') !== null && ! DB::table('institutes')->where('id', $record->institute_id)->where('tenant_id', $tenantId)->exists()) {
            $this->fail('institute_id', 'Institute must belong to the same tenant.');
        }
        if ($record->getAttribute('academic_year_id') !== null) {
            $year = AcademicYear::withoutGlobalScopes()->where('id', $record->academic_year_id)->where('tenant_id', $tenantId)->first();
            if (! $year || ($record->getAttribute('institute_id') && $year->institute_id !== null && (int) $year->institute_id !== (int) $record->institute_id)) {
                $this->fail('academic_year_id', 'Academic year must belong to the same tenant and institute boundary.');
            }
            if ($record->isDirty() && $year?->isReadOnly()) {
                $this->fail('academic_year_id', 'Locked or closed academic years are read-only.');
            }
        }
        if ($record->exists && $record instanceof AcademicStructureVersion && $record->getOriginal('status') === 'published') {
            $this->fail('status', 'Published structures require a new revision.');
        }
        $this->validateContextRelationships($record);
    }

    private function validateContextRelationships(CanonicalAcademicModel $record): void
    {
        $table = $record->getTable();
        if ($table === 'academic_programmes' && (int) $record->duration_months < 1) {
            $this->fail('duration_months', 'Programme duration must be at least one month.');
        }
        if ($table === 'academic_courses') {
            $programme = DB::table('academic_programmes')->find($record->academic_programme_id);
            if (! $programme || $programme->status !== 'active') {
                $this->fail('academic_programme_id', 'Only an active programme can receive a course.');
            }
        }
        if ($table === 'programme_offerings') {
            $programme = DB::table('academic_programmes')->find($record->academic_programme_id);
            if (! $programme || $programme->status !== 'active' || (int) $programme->institute_id !== (int) $record->institute_id) {
                $this->fail('academic_programme_id', 'Offering requires an active programme in the same institute.');
            }
        }
        if ($table === 'programme_course_offerings') {
            $offering = DB::table('programme_offerings')->find($record->programme_offering_id);
            $course = DB::table('academic_courses')->find($record->academic_course_id);
            if (! $offering || ! $course || (int) $offering->academic_programme_id !== (int) $course->academic_programme_id) {
                $this->fail('academic_course_id', 'Course must belong to the offered programme.');
            }
        }
        if ($table === 'academic_sections') {
            $hasClass = $record->academic_class_id !== null;
            $hasProgramme = $record->programme_offering_id !== null;
            if ($hasClass === $hasProgramme) {
                $this->fail('type', 'A section or batch must reference exactly one class or programme offering.');
            }
            $source = DB::table($hasClass ? 'academic_classes' : 'programme_offerings')->find($hasClass ? $record->academic_class_id : $record->programme_offering_id);
            $this->sameContext($record, $source, 'section source');
        }
        if ($table === 'semester_offerings') {
            $offering = DB::table('programme_offerings')->find($record->programme_offering_id);
            $semester = DB::table('semesters')->find($record->semester_id);
            $this->sameContext($record, $offering, 'programme offering');
            if (! $offering || ! $semester || (int) $offering->academic_programme_id !== (int) $semester->academic_programme_id) {
                $this->fail('semester_id', 'Semester must belong to the offered programme.');
            }
        }
        if ($table === 'class_subject_mappings') {
            $class = DB::table('academic_classes')->find($record->academic_class_id);
            $subject = DB::table('academic_subjects')->find($record->academic_subject_id);
            $this->sameContext($record, $class, 'class');
            if (! $subject || $subject->status !== 'active') {
                $this->fail('academic_subject_id', 'Only an active subject can be mapped.');
            }
        }
        if ($table === 'programme_subject_mappings') {
            $programme = DB::table('academic_programmes')->find($record->academic_programme_id);
            $course = $record->academic_course_id ? DB::table('academic_courses')->find($record->academic_course_id) : null;
            $semester = DB::table('semesters')->find($record->semester_id);
            $subject = DB::table('academic_subjects')->find($record->academic_subject_id);
            if (! $programme || ! $semester || (int) $semester->academic_programme_id !== (int) $programme->id || ($course && (int) $course->academic_programme_id !== (int) $programme->id)) {
                $this->fail('academic_programme_id', 'Programme, course, and semester must share one programme structure.');
            }
            if (! $subject || $subject->status !== 'active') {
                $this->fail('academic_subject_id', 'Only an active subject can be mapped.');
            }
        }
        if ($table === 'subject_offerings') {
            $school = $record->class_subject_mapping_id !== null;
            $college = $record->programme_subject_mapping_id !== null;
            if ($school === $college) {
                $this->fail('delivery_key', 'A subject offering must resolve to exactly one curriculum mapping.');
            }
            $mapping = DB::table($school ? 'class_subject_mappings' : 'programme_subject_mappings')->find($school ? $record->class_subject_mapping_id : $record->programme_subject_mapping_id);
            $this->sameContext($record, $mapping, 'curriculum mapping');
            if (! $mapping || (int) $mapping->academic_subject_id !== (int) $record->academic_subject_id) {
                $this->fail('academic_subject_id', 'Subject must match its curriculum mapping.');
            }
        }
    }

    private function sameContext(CanonicalAcademicModel $record, ?object $source, string $label): void
    {
        if (! $source || (int) $source->tenant_id !== (int) $record->tenant_id
            || (isset($source->institute_id) && (int) $source->institute_id !== (int) $record->institute_id)
            || (isset($source->academic_year_id) && (int) $source->academic_year_id !== (int) $record->academic_year_id)) {
            $this->fail('tenant_id', ucfirst($label).' must share the academic context.');
        }
    }

    private function nonNegative(Model $record, array $fields): void
    {
        foreach ($fields as $field) {
            if ($record->getAttribute($field) !== null && (float) $record->getAttribute($field) < 0) {
                $this->fail($field, ucfirst(str_replace('_', ' ', $field)).' cannot be negative.');
            }
        }
    }

    private function references(string $table): array
    {
        return match ($table) {
            'academic_courses' => ['academic_programme_id' => 'academic_programmes'],
            'programme_offerings' => ['academic_programme_id' => 'academic_programmes'],
            'programme_course_offerings' => ['programme_offering_id' => 'programme_offerings', 'academic_course_id' => 'academic_courses'],
            'academic_classes' => ['education_level_id' => 'education_levels', 'academic_course_id' => 'academic_courses'],
            'academic_sections' => ['academic_class_id' => 'academic_classes', 'programme_offering_id' => 'programme_offerings'],
            'subject_groups' => [],
            'academic_terms' => [],
            'semesters' => ['academic_programme_id' => 'academic_programmes'],
            'semester_offerings' => ['programme_offering_id' => 'programme_offerings', 'semester_id' => 'semesters', 'academic_term_id' => 'academic_terms'],
            'class_subject_mappings' => ['academic_class_id' => 'academic_classes', 'academic_subject_id' => 'academic_subjects', 'subject_group_id' => 'subject_groups', 'academic_term_id' => 'academic_terms', 'academic_structure_version_id' => 'academic_structure_versions'],
            'programme_subject_mappings' => ['academic_programme_id' => 'academic_programmes', 'academic_course_id' => 'academic_courses', 'semester_id' => 'semesters', 'academic_subject_id' => 'academic_subjects', 'subject_group_id' => 'subject_groups', 'academic_structure_version_id' => 'academic_structure_versions'],
            'subject_offerings' => ['academic_subject_id' => 'academic_subjects', 'class_subject_mapping_id' => 'class_subject_mappings', 'programme_subject_mapping_id' => 'programme_subject_mappings', 'academic_section_id' => 'academic_sections', 'semester_offering_id' => 'semester_offerings', 'academic_term_id' => 'academic_terms'],
            default => [],
        };
    }

    private function fail(string $key, string $message): never
    {
        throw ValidationException::withMessages([$key => $message]);
    }
}
