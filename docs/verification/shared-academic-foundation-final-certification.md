# Shared Academic Foundation Final Certification

Date: 2026-08-04  
Baseline: `09ff147` plus the implementation milestone described below

## Executive decision

**CERTIFIED WITH CONDITIONS**

The backend now provides one tenant-owned canonical academic spine for school and college structures. Attendance, Timetable, Examination, Lesson Planning, and Homework can consume academic context and subject offerings through shared provider contracts without defining alternative class, section, programme, semester, or subject masters.

Conditions are limited to delivery hardening: run the same suite against the production MySQL/PostgreSQL driver, and require the existing authorization/module middleware on any future academic management HTTP screens. No such management screens or operational modules were introduced by this milestone.

## Architecture and identifiers

```text
AcademicYear
├── AcademicClass ── AcademicSection ── ClassSubjectMapping ── SubjectOffering
│                                      └── AcademicSubject
└── ProgrammeOffering ── SemesterOffering ── ProgrammeSubjectMapping ── SubjectOffering
    ├── AcademicProgramme ── AcademicCourse
    └── ProgrammeCourseOffering
```

Canonical identifiers are `academic_year_id`, `academic_class.id` (`class_id` at consumer boundaries), `academic_section.id` (`section_id`), `programme_offering_id`, `programme_course_offering_id`, `semester_offering_id`, `academic_subject.id` (`subject_id`), `subject_offering_id`, and `academic_term.id` (`term_id`). Consumers receive these records through contracts; they do not own substitute masters.

## Deterministic proof structures

```text
Tenant A / Company A / Campus A
├── School A / 2026–27 / Secondary / Class 10 / Section A
│   └── English / Term 1 / class mapping / ENG-10-A offering
└── College A / 2026–27 / B.Tech / CSE / Semester 1
    └── curriculum v1 / CS101-BATCH-A offering

Tenant B
└── hostile course and relationship substitutions (rejected)
```

The fixed fixture also proves School A records cannot be placed in College A context. Context providers reject wrong tenant, institute, and year combinations.

## Relationship and isolation matrix

| Boundary | Enforced by | Result |
|---|---|---|
| Tenant → every canonical record | global scope, explicit provider predicates, validator | PASS |
| Institute → class/programme/section/offering | indexed context columns and validator | PASS |
| Year → structure/mapping/offering | indexed context columns, FK, lock validator | PASS |
| Programme → course/semester | compatibility validator and unique indexes | PASS |
| Class → section/mapping | context validator and FK | PASS |
| Mapping → subject offering | exactly-one-source and subject-match validator | PASS |
| Published curriculum | immutable version validator | PASS |
| Clone source → destination | same-tenant transaction with regenerated UUID/PK | PASS |
| Operational/transactional records during clone | excluded | PASS |

## Consumer proof

| Consumer | Shared provider proof | Result |
|---|---|---|
| Attendance | year, class/programme, section/semester, offering | PASS |
| Timetable | cohort, term/semester, subject offering | PASS |
| Examination | mapping-backed offering, marks and credits | PASS |
| Lesson Planning | offering plus active teaching context | PASS |
| Homework | canonical cohort and subject offering | PASS |

Contracts: `AcademicContextProvider`, `ClassSectionProvider`, `ProgrammeSemesterProvider`, `SubjectOfferingProvider`, `AcademicCalendarProvider`, and `AcademicNomenclatureProvider`.

## Verification results

| Check | Actual result |
|---|---|
| Fresh migrate and seed | PASS, 17 migrations and all seeders |
| Full regression | PASS, 86 tests / 221 assertions |
| `academics` | PASS, 21 / 35 |
| `academic-structure` | PASS, 7 / 18 |
| `integration-contract` | PASS, 6 / 15 |
| `tenancy` | PASS, 8 / 18 |
| `isolation` | PASS, 17 / 35 |
| `authorization` | PASS, 7 / 17 |
| `navigation` | PASS, 9 / 17 |
| `audit` | PASS, 8 / 29 |
| Academic structure audit | PASS; all tables, duplicate boundaries, and orphan offerings |
| Navigation audit | PASS; all published routes and permissions valid |
| PHPStan | PASS |
| Pint | PASS |
| Vite production build | PASS |

## Duplicate-structure report

No duplicate active classes, sections, programmes, or subject-offering delivery keys were found. No orphan subject offerings were found. Database uniqueness prevents their insertion within configured boundaries. Cross-tenant and cross-institute hostile relationships are rejected before persistence.

## Security, history, and audit

- Locked/closed academic years reject structure writes.
- Published structure versions require a new revision.
- Canonical records are soft-deletable and foreign keys restrict deletion where historical records depend on them.
- Cloning is transactional, regenerates identifiers, excludes subject offerings and all operational transactions, and emits `academic.structure.cloned` with source, destination, institute, selected components, and counts.
- Dead navigation links are seeded inactive and are therefore neither displayed nor searchable.
- Academic permissions use the shared granular permission catalogue.

## Principal implementation files

- `database/migrations/2026_08_04_030000_create_canonical_academic_structure.php` — canonical schema, foreign keys, indexes, uniqueness.
- `app/Domains/Academics/Models/*` — tenant-owned canonical records.
- `app/Domains/Academics/Services/AcademicStructureValidator.php` — ownership, context, numeric, date, lifecycle, and mapping invariants.
- `app/Domains/Academics/Services/CanonicalAcademicProvider.php` — read-only consumer resolution.
- `app/Domains/Academics/Services/AcademicStructureCloneService.php` — safe transactional cloning and audit.
- `tests/Feature/Academics/IntegrationContracts/CanonicalAcademicSpineTest.php` — deterministic school, college, isolation, lock, clone, and consumer proof.
- `app/Console/Commands/AcademicStructureAuditCommand.php` and `NavigationAuditCommand.php` — production verification commands.

The certification applies to the shared backend academic foundation. Future operational modules must depend on the provider contracts and canonical identifiers above; introducing module-specific academic master tables invalidates this certification.
