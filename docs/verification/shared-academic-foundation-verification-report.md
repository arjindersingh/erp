# Shared Academic Foundation Verification Report

Verification date: 2026-08-04  
Verified commit baseline: `5f96a5d`

## A. Executive summary

| Measure | Result |
|---|---|
| Overall status | Critical failure |
| Critical failures | 16 missing canonical tables; no consumer contracts; no navigation audit command |
| High-risk failures | No structural policies/audits/versioning/cloning; five required test groups contain zero tests |
| Warnings | Repository-wide Pint check finds five pre-existing formatting failures |
| Passed tests | Academics 15/15; tenancy 8/8; isolation 11/11 |
| Recommendation | **NOT CERTIFIED** |

The application has a sound beginning—tenant-aware catalogues and academic-year context—but does not yet contain the academic spine described by the certification prompt. Passing tests cover only components that exist and do not prove school/college canonical structure.

## B. Environment

| Item | Value |
|---|---|
| Laravel | 13.23.0 |
| PHP | 8.4.15 |
| Database | SQLite (verification environment) |
| Cache | Database; PHPUnit overrides to array |
| Queue | Database; PHPUnit overrides to sync |
| Testing | PHPUnit 12 / Pest integration |
| Git baseline | `5f96a5d` |
| Production assets | Vite 8.2.0; build passed |

`migrate:fresh --seed --env=testing` completed all 16 migrations and all configured seeders successfully.

## C. Component results

| Component | Tests | Passed | Failed | Status |
|---|---:|---:|---:|---|
| Academic years | 10 | 10 | 0 | Partial pass |
| Education catalogues/nomenclature | 5 | 5 | 0 | Partial pass |
| Programmes and courses | 0 | 0 | 0 | Missing |
| Classes and sections | 0 | 0 | 0 | Missing |
| Semesters | 0 | 0 | 0 | Missing |
| Subjects and groups | 0 | 0 | 0 | Missing |
| Class-subject mappings | 0 | 0 | 0 | Missing |
| Programme-subject mappings | 0 | 0 | 0 | Missing |
| Subject offerings | 0 | 0 | 0 | Missing |
| Structure cloning/versioning | 0 | 0 | 0 | Missing |
| Tenant isolation (platform/current catalogues) | 8 | 8 | 0 | Pass, incomplete model coverage |
| Institute isolation | 0 | 0 | 0 | Not proven |
| Academic permissions and policies | 0 | 0 | 0 | Not proven |
| Dynamic academic menus | 0 | 0 | 0 | Not proven |
| Academic audit logging | 0 | 0 | 0 | Not proven |
| Consumer contracts | 0 | 0 | 0 | Missing |

## D. Isolation matrices

The required deterministic Tenant A/Tenant B and School/College topology has not been implemented as a certification fixture.

| Boundary | List/search/view | Cross-link create | Update/publish/clone | Result |
|---|---|---|---|---|
| Tenant A → Tenant B, existing catalogues | Tested | Tested selectively | Not fully tested | Partial pass |
| School A → College A | Not tested | Not tested | Not tested | Not proven |
| Institute A → Institute A2 | Not tested | Not tested | Not tested | Not proven |
| Queue/API/import/cache substitution | Not tested | Not tested | Not tested | Not proven |

## E. Duplicate-structure findings

The audit returned exit code `1`. It reported all of these canonical tables missing:

```text
academic_programmes, programme_offerings, academic_courses,
programme_course_offerings, academic_classes, academic_sections,
academic_subjects, subject_groups, academic_terms, semesters,
semester_offerings, class_subject_mappings, programme_subject_mappings,
subject_offerings, academic_calendars, academic_structure_versions
```

Consequently duplicate, orphan, cross-institute, invalid curriculum, inactive-mapping, and subject-offering checks cannot run. Existing academic-year `2026-2027` passed its date-range check.

## F. Consumer-module proof

| Module | Required contract | Status |
|---|---|---|
| Attendance | Canonical academic context | FAIL—classes, sections, offerings absent |
| Timetable | Canonical subject offering | FAIL—subject offerings absent |
| Examination | Canonical curriculum mapping | FAIL—mappings absent |
| Lesson Planning | Canonical teaching context | FAIL—offerings absent |
| Homework | Canonical cohort and subject | FAIL—sections/batches and offerings absent |

No operational module was created during verification.

## G. Command evidence

| Command | Actual result |
|---|---|
| `php artisan migrate:fresh --seed --env=testing` | PASS |
| `php artisan test --group=academics` | PASS, 15 tests / 20 assertions |
| `php artisan test --group=academic-structure` | FAIL, no tests found |
| `php artisan test --group=integration-contract` | FAIL, no tests found |
| `php artisan test --group=tenancy` | PASS, 8 tests / 18 assertions |
| `php artisan test --group=isolation` | PASS, 11 tests / 20 assertions |
| authorization/navigation/audit groups | FAIL, no tests found |
| `php artisan erp:academic-structure-audit` | FAIL, exit 1; 16 missing tables |
| `php artisan erp:navigation-audit` | FAIL, command undefined |
| `vendor/bin/phpstan analyse` | PASS, no errors |
| `vendor/bin/pint --test` | FAIL, 5 existing files require formatting |
| `npm run build` | PASS |

Static pattern review found scoped bypasses in academic resolvers, validators, selection, and audit code. Resolver and selection bypasses explicitly restore tenant filters; validator lookups compare tenant ownership. These still require dedicated bypass tests and documentation before certification.

## H. Certification decision

# NOT CERTIFIED

This decision is mandatory under failure conditions 4–7, 10, 14, and the exit criteria. The current implementation cannot prove a canonical subject offering, curriculum history, consumer reuse, or complete academic auditing.

## I. Remediation plan and commit sequence

1. `feat(academics): add canonical programmes classes and isolation boundaries`
2. `feat(academics): add subjects semesters mappings and offerings`
3. `feat(academics): add calendars curriculum versions and safe cloning`
4. `feat(academics): enforce academic policies navigation and audits`
5. `test(academics): certify canonical consumer contracts`
6. `docs(academics): issue shared foundation certification report`

Each implementation milestone must add deterministic school/college fixtures, test all ownership boundaries, run the prescribed groups, extend the fail-closed audit, and commit only after its tests pass. Certification can be reconsidered only after all missing components and evidence suites exist.
