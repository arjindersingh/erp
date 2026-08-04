<?php

declare(strict_types=1);

namespace App\Domains\Academics\Services;

use App\Core\AcademicYear\AcademicYearContext;
use App\Domains\Academics\Contracts\AcademicCalendarProvider;
use App\Domains\Academics\Contracts\AcademicContextProvider;
use App\Domains\Academics\Contracts\ClassSectionProvider;
use App\Domains\Academics\Contracts\ProgrammeSemesterProvider;
use App\Domains\Academics\Contracts\SubjectOfferingProvider;
use App\Domains\Academics\Models\AcademicCalendar;
use App\Domains\Academics\Models\AcademicClass;
use App\Domains\Academics\Models\AcademicSection;
use App\Domains\Academics\Models\ProgrammeOffering;
use App\Domains\Academics\Models\SemesterOffering;
use App\Domains\Academics\Models\SubjectOffering;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class CanonicalAcademicProvider implements AcademicCalendarProvider, AcademicContextProvider, ClassSectionProvider, ProgrammeSemesterProvider, SubjectOfferingProvider
{
    public function classesForContext(AcademicYearContext $context): Collection
    {
        [$year, $scope] = $this->context($context);

        return AcademicClass::query()->where('tenant_id', $year->tenant_id)->where('institute_id', $scope->institute_id)->where('academic_year_id', $year->id)->where('status', 'active')->get();
    }

    public function programmesForContext(AcademicYearContext $context): Collection
    {
        [$year, $scope] = $this->context($context);

        return ProgrammeOffering::query()->where('tenant_id', $year->tenant_id)->where('institute_id', $scope->institute_id)->where('academic_year_id', $year->id)->where('status', 'active')->get();
    }

    public function sectionsForClass(AcademicClass $class, AcademicYearContext $context): Collection
    {
        [$year, $scope] = $this->context($context);
        $this->matches($class, $year->tenant_id, $scope->institute_id, $year->id);

        return AcademicSection::query()->where('academic_class_id', $class->id)->where('status', 'active')->get();
    }

    public function semesterOfferingsForProgramme(ProgrammeOffering $offering): Collection
    {
        return SemesterOffering::query()->where('tenant_id', $offering->tenant_id)->where('institute_id', $offering->institute_id)->where('academic_year_id', $offering->academic_year_id)->where('programme_offering_id', $offering->id)->where('status', 'active')->get();
    }

    public function forSection(AcademicSection $section, AcademicYearContext $context): Collection
    {
        [$year, $scope] = $this->context($context);
        $this->matches($section, $year->tenant_id, $scope->institute_id, $year->id);

        return SubjectOffering::query()->where('academic_section_id', $section->id)->where('status', 'active')->get();
    }

    public function forSemester(SemesterOffering $semester, AcademicYearContext $context): Collection
    {
        [$year, $scope] = $this->context($context);
        $this->matches($semester, $year->tenant_id, $scope->institute_id, $year->id);

        return SubjectOffering::query()->where('semester_offering_id', $semester->id)->where('status', 'active')->get();
    }

    public function forContext(AcademicYearContext $context): Collection
    {
        [$year, $scope] = $this->context($context);

        return AcademicCalendar::query()->where('tenant_id', $year->tenant_id)->where('institute_id', $scope->institute_id)->where('academic_year_id', $year->id)->orderBy('starts_at')->get();
    }

    private function context(AcademicYearContext $context): array
    {
        $year = $context->requireYear();
        $scope = $context->scope();
        if (! $scope?->institute_id) {
            throw ValidationException::withMessages(['scope' => 'An institute scope is required.']);
        }

        return [$year, $scope];
    }

    private function matches(object $record, int $tenantId, int $instituteId, int $yearId): void
    {
        if ((int) $record->tenant_id !== $tenantId || (int) $record->institute_id !== $instituteId || (int) $record->academic_year_id !== $yearId) {
            throw ValidationException::withMessages(['context' => 'Academic record is outside the active context.']);
        }
    }
}
