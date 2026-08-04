<?php

use App\Core\Organization\Campus;
use App\Core\Organization\Company;
use App\Core\Organization\Institute;
use App\Domains\Academics\Models\AcademicCalendar;
use App\Domains\Academics\Models\AcademicClass;
use App\Domains\Academics\Models\AcademicCourse;
use App\Domains\Academics\Models\AcademicNomenclatureSetting;
use App\Domains\Academics\Models\AcademicProgramme;
use App\Domains\Academics\Models\AcademicSection;
use App\Domains\Academics\Models\AcademicStructureVersion;
use App\Domains\Academics\Models\AcademicSubject;
use App\Domains\Academics\Models\AcademicTerm;
use App\Domains\Academics\Models\AcademicYear;
use App\Domains\Academics\Models\AcademicYearLock;
use App\Domains\Academics\Models\AcademicYearScopeAssignment;
use App\Domains\Academics\Models\ClassSubjectMapping;
use App\Domains\Academics\Models\EducationAuthority;
use App\Domains\Academics\Models\EducationLevel;
use App\Domains\Academics\Models\InstituteAuthorityAffiliation;
use App\Domains\Academics\Models\ProgrammeCourseOffering;
use App\Domains\Academics\Models\ProgrammeOffering;
use App\Domains\Academics\Models\ProgrammeSubjectMapping;
use App\Domains\Academics\Models\Semester;
use App\Domains\Academics\Models\SemesterOffering;
use App\Domains\Academics\Models\SubjectGroup;
use App\Domains\Academics\Models\SubjectOffering;

return [
    'owned_models' => [
        Company::class,
        Campus::class,
        Institute::class,
        AcademicNomenclatureSetting::class,
        EducationLevel::class,
        EducationAuthority::class,
        AcademicYear::class,
        AcademicYearScopeAssignment::class,
        AcademicYearLock::class,
        InstituteAuthorityAffiliation::class,
        AcademicProgramme::class, AcademicCourse::class, ProgrammeOffering::class,
        ProgrammeCourseOffering::class, AcademicClass::class, AcademicSection::class,
        AcademicSubject::class, SubjectGroup::class, AcademicTerm::class, Semester::class,
        SemesterOffering::class, AcademicStructureVersion::class, ClassSubjectMapping::class,
        ProgrammeSubjectMapping::class, SubjectOffering::class, AcademicCalendar::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Public Tenant Resolution
    |--------------------------------------------------------------------------
    |
    | Public pages are resolved from the request host before authentication.
    | This lets the public portal, login screens, and open access pages render
    | in the correct tenant context.
    |
    */
    'public_resolution' => [
        'enabled' => env('TENANCY_PUBLIC_RESOLUTION_ENABLED', true),

        /*
         * When true, localhost and loopback hosts will not require a tenant.
         * This keeps local development and health checks convenient.
         */
        'allow_central_domains' => env('TENANCY_ALLOW_CENTRAL_DOMAINS', true),

        'central_domains' => array_filter(array_map(
            'trim',
            explode(',', env('TENANCY_CENTRAL_DOMAINS', 'localhost,127.0.0.1,::1'))
        )),

        /*
         * Supported mode: domain.
         * Subdomain/path modes can be added later without changing callers.
         */
        'strategy' => env('TENANCY_PUBLIC_RESOLUTION_STRATEGY', 'domain'),

        /*
         * In public mode, unknown domains are rejected by default to avoid
         * accidentally rendering one tenant's data for another host.
         */
        'fail_on_unknown_domain' => env('TENANCY_FAIL_ON_UNKNOWN_DOMAIN', true),
    ],
];
