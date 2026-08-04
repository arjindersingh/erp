# Shared Academic Foundation — Architecture and Milestone 1 Report

Date: 2026-08-04

## Status

Milestone 1 is implemented and verified. The complete academic foundation remains **IN PROGRESS**: programmes, courses, classes, sections, semesters, subjects, mappings, offerings, calendars, structure versioning, cloning, policies, workflows, UI, APIs, imports, exports, and notifications belong to later milestones.

The earlier platform report remains `NOT CERTIFIED`; this implementation does not overwrite that evidence with the new brief's assumption that every shared-platform component has passed.

## Canonical architecture

Catalogue definitions are separated from dated institute occurrences. Platform or tenant catalogues provide education levels and authorities. Institute affiliations connect institutes to compatible authorities. Academic years establish the dated scope consumed by every later academic structure. Stable entity keys drive logic; nomenclature overrides only presentation.

```text
Platform/Tenant catalogues
├── EducationLevel
└── EducationAuthority
        │
Tenant → Company → Campus → Institute
        │                         ├── AuthorityAffiliation
        │                         ├── AcademicYear
        │                         └── Nomenclature overrides
        └──────────────────────────── tenant ownership boundary
```

## School hierarchy

```text
Institute
└── Academic Year
    └── Education Level
        └── Class
            ├── optional Course/Stream
            └── Section
                └── Subject Offering
```

## College hierarchy

```text
Institute
└── Academic Year
    └── Programme Offering
        └── optional Course/Specialisation Offering
            └── Semester Offering (dated occurrence)
                └── Section/Batch
                    └── Subject Offering
```

## Entity distinctions

| Entity | Canonical meaning |
| --- | --- |
| Programme | Qualification or learning path, such as B.Tech or B.Ed. |
| Course | Optional route within a programme or class: stream, branch, major, specialisation. |
| Class | Ordered school grade or annual stage; never a substitute for a semester. |
| Section | Learner cohort: section, batch, tutorial, laboratory, or elective group. |
| Subject | Reusable curriculum unit. |
| Term | Dated period inside an academic year. |
| Semester | Reusable structural stage; its dated delivery is a semester offering. |

## Nomenclature strategy

Internal keys remain `academic_year`, `programme`, `course`, `class`, `section`, `subject`, `term`, and `semester`. Labels resolve in this order:

```text
Institute → Campus → Company → Tenant → system default
exact locale → fallback locale
```

For example, `section` may display as “Section” in a school and “Batch” in a college. Routes, permissions, table names, APIs, and decisions continue to use `section`.

## Database relationships

```text
education_levels (platform|tenant)

education_authorities (platform|tenant)
└── institute_authority_affiliations
    └── institutes (tenant + company + campus composite FK)

academic_nomenclature_settings
└── tenant / optional company / optional campus / optional institute

academic_years
└── tenant / optional company / optional campus / optional institute
```

Nullable boundaries use deterministic `ownership_key` or `boundary_key` columns with unique indexes. This avoids SQL `NULL` uniqueness semantics permitting duplicate platform or tenant-level records. Cross-tenant institute relationships use composite foreign keys, while an affiliation model rule allows only platform authorities or authorities owned by the same tenant.

## Migration execution order

Current milestone:

1. `academic_nomenclature_settings`
2. `education_levels`
3. `education_authorities`
4. `academic_years`
5. `institute_authority_affiliations`

Planned dependency order:

6. academic programmes
7. programme offerings
8. courses
9. programme-course offerings
10. classes
11. semesters
12. semester offerings
13. sections
14. subjects and subject groups
15. terms
16. class/programme subject mappings
17. subject offerings
18. calendars and events
19. academic structure versions

## Risks and controls

| Risk | Control |
| --- | --- |
| Operational modules duplicate masters | Canonical models and future `subject_offering_id` integration contract |
| Labels alter business logic | Backed entity-key enums and presentation-only service |
| Curriculum edits corrupt history | Definition/offering separation; later version and publication workflow |
| Tenant catalogue leakage | shared-or-active-tenant global scope |
| Cross-tenant affiliation | model validation plus composite institute FK |
| Duplicate records at nullable boundaries | deterministic keys with database uniqueness |
| Invalid or mutable historical years | date validation, single-default rule, read-only locked/closed/archived state |
| Browser submits ownership IDs | future write actions must derive scope from validated active context; no controllers are introduced here |

## Milestone implementation

- Six backed enums for stable terminology, categories, authority types, statuses, and academic-year lifecycle.
- Five production tables, five models, and five factories.
- `AcademicNomenclatureService` with specificity and locale fallback.
- `AcademicYearResolver` with institute-to-tenant fallback.
- System level/authority/default-label seeding.
- Academics module and foundational permissions.
- `erp:academic-structure-audit` and expanded `erp:data-ownership-audit` coverage.
- Nine feature tests covering eleven assertions.

## Verification commands

```bash
php artisan migrate:fresh --seed --env=testing
php artisan test --group=academics
php artisan test --group=isolation
php artisan test
vendor/bin/phpstan analyse app/Domains/Academics app/Console/Commands database/factories database/seeders --memory-limit=1G
php artisan erp:academic-structure-audit
php artisan erp:data-ownership-audit
```

## Operational-module integration contract

Admissions and enrolment will select canonical programme/class/section offerings. Attendance, timetable, examination, homework, lesson planning, teacher assignments, and marks will reference the future `subject_offerings.id`. They must not create module-specific academic years, classes, sections, programmes, semesters, subjects, or mappings.

## Planned commit sequence

1. Academic catalogues, nomenclature, affiliations, and academic years.
2. Programmes, courses, and institute offerings.
3. Classes, semesters, sections, and subjects.
4. Curriculum mappings and subject offerings.
5. Calendars, structure versions, validation, and cloning.
6. Policies, workflows, routes, menus, UI, imports/exports, audit, notifications, and APIs.
7. Final isolation, performance, and integration certification.
