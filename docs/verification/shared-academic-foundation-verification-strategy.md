# Shared Academic Foundation Verification Strategy

Verification date: 2026-08-04

## Architecture review

The repository currently implements the organisation hierarchy, tenant scoping, memberships, permissions, navigation, audit infrastructure, academic years, education levels, education authorities, institute affiliations, and presentation nomenclature. It does **not** yet implement the canonical structural spine required for certification: programmes, programme/course offerings, classes, sections/batches, subjects/groups, terms, semesters/offerings, mappings, subject offerings, calendars, versions, or cloning.

Certification therefore begins with a fail-closed schema audit. Missing canonical tables are critical failures, not warnings.

## Deterministic verification topology

```text
Tenant A / Company A / Campus A
├── School A / 2026–27
│   ├── Secondary / Class 10 / Sections A and B
│   └── Senior Secondary / Class 11 / Medical, Non-Medical, Commerce, Humanities
└── College A / 2026–27
    ├── B.Tech / CSE / Semesters 1 and 2 / Batch A
    └── B.Ed. / Semesters 1 and 2

Tenant B / Company B / Campus B
├── School B
└── College B
```

Fixed codes and names will be used in a dedicated certification seeder. Tenant B mirrors only the organisational shape and supplies hostile cross-tenant references.

## Canonical identifier map

| Identifier | Canonical owner | Consumers |
|---|---|---|
| `academic_year_id` | `academic_years` | all academic consumers |
| `class_id` | `academic_classes` | attendance, timetable, admissions, promotion |
| `section_id` | `academic_sections` | attendance, timetable, homework |
| `programme_offering_id` | `programme_offerings` | college consumers |
| `programme_course_offering_id` | `programme_course_offerings` | college consumers |
| `semester_offering_id` | `semester_offerings` | attendance, timetable, examination |
| `subject_id` | `academic_subjects` | curriculum foundation only |
| `subject_offering_id` | `subject_offerings` | attendance, timetable, examination, lessons, homework |
| `term_id` | `academic_terms` | school and term-based consumers |

No consumer-specific class, section, programme, semester, or subject master is permitted.

## Consumer contract model

Planned read-only contracts are `AcademicContextProvider`, `ClassSectionProvider`, `ProgrammeSemesterProvider`, `SubjectOfferingProvider`, `AcademicCalendarProvider`, and `AcademicNomenclatureProvider`. Contract tests will use lightweight Attendance, Timetable, Examination, Lesson Planning, and Homework consumers. They will store no operational records and create no alternative masters.

## Test suite paths

The certification suite will occupy the prompt-prescribed directories under `tests/Feature/Academics/`: `AcademicYears`, `EducationLevels`, `Authorities`, `Programmes`, `Courses`, `Classes`, `Sections`, `Subjects`, `Terms`, `Semesters`, `Mappings`, `SubjectOfferings`, `Nomenclature`, `Cloning`, `Navigation`, `Audit`, `Isolation`, and `IntegrationContracts`, plus `tests/Unit/Academics`.

## Highest risks

1. Missing subject offerings leave every future operational consumer without a canonical delivery unit.
2. Missing mappings permit modules to invent incompatible class/subject and programme/subject relationships.
3. Institute isolation is not enforceable for models that do not exist.
4. The previous audit only inspected existing records and falsely passed when required structures were absent.
5. Academic-year locking is not yet wired into absent structural mutation services.
6. No publication/version or clone boundary exists to preserve historical curricula.
7. No academic model currently emits the required domain-specific audit events.

## Milestone order

1. Academic years, programmes/courses, classes/sections, tenant and institute isolation, fail-closed structure audit.
2. Subjects, groups, terms, semesters, mappings, offerings, calendars, and versions.
3. Nomenclature, cloning, permissions, navigation, and audit trails.
4. Provider contracts, five consumer simulations, performance checks, full command matrix, and certification report.

The current certification state is **NOT CERTIFIED** until all four milestones pass.
