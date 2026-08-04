# Academic people and workforce foundation

## Conceptual architecture

Identity and authority are deliberately separate. `Person` is the human identity; `EmployeeProfile` is that person's tenant employment identity; `EmploymentAssignment` is an official, dated posting. Later milestones attach teaching assignments, functional responsibilities, committee memberships, acting appointments, and delegations to an employment assignment without rewriting its designation.

```text
Person 1──* tenant identities (EmployeeProfile: max one active per tenant)
EmployeeProfile 1──* EmploymentAssignment
EmploymentAssignment *──1 Institute / Department / Designation / JobPost
EmploymentAssignment 1──* TeachingAssignment
EmploymentAssignment 1──* ResponsibilityAssignment ──* Delegation
EmploymentAssignment 1──* FunctionalUnitMembership
EmploymentAssignment 1──* ActingAssignment
```

Teaching answers “what is taught” through the canonical subject offering and academic year. A responsibility answers “what additional duty is performed.” Membership records formal participation but grants no access by itself. Acting appointments are time-bound additional charge. Delegation transfers a subset of existing authority for a mandatory interval and never creates authority.

## Assignment source and authorization

```text
dated source assignment
  → explicit scoped role assignment (source type + source id)
  → role permissions (explicit deny wins)
  → enabled module + matching portal
  → active tenant/company/campus/institute/academic-year context
  → policy + workflow transition
```

Source revocation or expiry ends its linked roles and invalidates permission/menu caches. An assignment never grants permissions directly. Delegation validation intersects requested permissions with the delegator's effective permissions and segregation-of-duties rules.

## Active context

Compatible duties in the same tenant and institute may be resolved together. Duties in different institutes are separate selectable contexts and are never blended. Resolution filters dates, status, portal, module, access scope, academic year, and employment validity before authorization is evaluated.

## Migration order

1. designation_categories
2. designations
3. job_categories
4. employment_types
5. employment_statuses
6. departments
7. job_posts
8. employee_profiles
9. employment_assignments
10. employment_assignment_histories
11. teacher_profiles and teaching_assignment_types
12. academic teaching/class/programme/department-head assignments
13. functional_unit_types and functional_units
14. responsibility_types and employee_responsibility_assignments
15. functional_unit_memberships
16. responsibility_delegations and delegated_permissions
17. acting_assignments
18. workload types and allocations
19. role-assignment source extensions

## Principal risks and controls

- Cross-tenant references: global scopes plus composite tenant foreign keys and service validation.
- Cross-institute blending: context resolution groups by organisational boundary; policies re-check it for direct URLs.
- Privilege escalation: assignment sources create only allow-listed scoped roles; explicit denies and module/portal checks remain authoritative.
- Delegation expansion: requested permissions must be a subset of source authority, have an end time, pass approval and segregation-of-duties checks, and remain in tenant/scope.
- History loss: used records are restricted/soft-deleted and assignment changes append effective-dated history.
- Circular leadership references: `primary_employment_assignment_id` and department head references are intentionally nullable; application validation owns them after assignment creation.

Milestone one implements the masters, employee profile, official assignments, models, factories, seed data, integrity validation, foundational tests, and an audit command that expands as later tables arrive. Teaching, responsibilities, UI, workflow, authorization sources, expiry processing, and operational integrations remain later milestones by design.

## School and college teaching flows

The next milestone will connect both delivery models through one mandatory `subject_offering_id`:

```text
School: Employee → Posting → Academic Year → Class → Section → Subject Offering
College: Employee → Posting → Academic Year → Programme Offering → Course → Semester Offering → Batch → Subject Offering
```

No free-text academic labels or module-specific teacher maps are permitted. Academic coordination remains a separate dated source for class, section, programme, semester, subject, or department leadership.

## Milestone-one operations

```bash
php artisan migrate
php artisan db:seed --class=WorkforceFoundationSeeder
php artisan test --group=workforce
php artisan erp:employee-teaching-assignment-audit
composer analyse
```

The integrity command audits the installed employee/posting tables now and reports uninstalled teacher, teaching, and coordination tables as milestone warnings. Those checks become active automatically when their tables exist.
