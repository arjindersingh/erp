<?php

declare(strict_types=1);

namespace App\Domains\Academics\Services;

use App\Core\Tenancy\TenantContext;
use App\Domains\Academics\Enums\AcademicEntityKey;
use App\Domains\Academics\Models\AcademicNomenclatureSetting;

final class AcademicNomenclatureService
{
    /** @var array<string, array{string, string}> */
    private const DEFAULTS = [
        'academic_year' => ['Academic Year', 'Academic Years'], 'programme' => ['Programme', 'Programmes'],
        'course' => ['Course', 'Courses'], 'class' => ['Class', 'Classes'], 'section' => ['Section', 'Sections'],
        'subject' => ['Subject', 'Subjects'], 'term' => ['Term', 'Terms'], 'semester' => ['Semester', 'Semesters'],
    ];

    public function __construct(private readonly TenantContext $tenantContext) {}

    public function label(
        string|AcademicEntityKey $entityKey,
        bool $plural = false,
        ?int $companyId = null,
        ?int $campusId = null,
        ?int $instituteId = null,
        ?string $locale = null,
    ): string {
        $this->tenantContext->requireTenant();
        $key = $entityKey instanceof AcademicEntityKey ? $entityKey->value : AcademicEntityKey::from($entityKey)->value;
        $locale ??= app()->getLocale();
        $query = AcademicNomenclatureSetting::query()->where('entity_key', $key)->where('status', 'active')
            ->where(fn ($query) => $query->where('locale', $locale)->orWhere('locale', config('app.fallback_locale', 'en')))
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $companyId))
            ->where(fn ($query) => $query->whereNull('campus_id')->orWhere('campus_id', $campusId))
            ->where(fn ($query) => $query->whereNull('institute_id')->orWhere('institute_id', $instituteId))
            ->orderByRaw('CASE WHEN institute_id IS NULL THEN 0 ELSE 8 END + CASE WHEN campus_id IS NULL THEN 0 ELSE 4 END + CASE WHEN company_id IS NULL THEN 0 ELSE 2 END + CASE WHEN locale = ? THEN 1 ELSE 0 END DESC', [$locale]);
        $setting = $query->first();

        return $setting?->{$plural ? 'plural_label' : 'singular_label'} ?? self::DEFAULTS[$key][$plural ? 1 : 0];
    }

    public function classLabel(): string
    {
        return $this->label(AcademicEntityKey::ClassModel);
    }

    public function sectionLabel(): string
    {
        return $this->label(AcademicEntityKey::Section);
    }

    public function subjectLabel(): string
    {
        return $this->label(AcademicEntityKey::Subject);
    }

    public function programmeLabel(): string
    {
        return $this->label(AcademicEntityKey::Programme);
    }

    public function courseLabel(): string
    {
        return $this->label(AcademicEntityKey::Course);
    }

    public function academicYearLabel(): string
    {
        return $this->label(AcademicEntityKey::AcademicYear);
    }
}
