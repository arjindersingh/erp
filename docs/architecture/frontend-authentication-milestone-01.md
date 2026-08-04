# Frontend authentication and UAT architecture — Milestone 1

## Foundation review and portal architecture

The repository provides host-based tenant resolution, canonical Person/Profile links, memberships, hierarchical access scopes, role assignments, permission catalogues, portals, tenant module activation, academic-year context, workforce employment, and student/guardian profiles. It did not provide login/context UI, session restoration, effective permission resolution for scoped role assignments, access diagnostics, UAT fixtures, or the claimed responsibility/committee/delegation foundation. Those missing layers are not treated as complete.

Surfaces are isolated: public admissions uses tenant-resolved unauthenticated routes; applicant secure access will use application identity rather than ERP authentication; administration/staff/management portals use authenticated memberships and scoped permissions; student/guardian portals additionally require canonical profile relationships and record policies.

## Request and profile pipeline

Authenticated requests execute: resolve host tenant → authenticate active/unlocked user → select and revalidate membership → validate scope/portal/year tuple from server-owned records → restore tenant and academic contexts → verify tenant module activation → resolve current role assignments and unexpired role grants → require permission → apply record policy/workflow lock. Known forbidden resources return 403; foreign records should be query-scoped to 404.

`AuthenticatedProfileResolver` follows User → tenant-specific active UserPersonLink → Person → generic Profiles plus canonical Employee/Student/Guardian profiles. It never infers profile from a role name. Future responsibility, committee, delegation, teacher, and applicant-access resolvers attach to the returned immutable profile set.

The session stores only membership UUID, portal code, and academic-year UUID. Every request reloads and validates them against the authenticated user and resolved tenant. Context selection regenerates the session. Organisation IDs are not accepted by the selector.

## UAT topology and personas

UAT creates Tenant A/B, each with one company, campus, School and College, plus 2027–28 academic year and intentionally parallel admission campaigns. Domains are `uat-a.erp-uat.test` and `uat-b.erp-uat.test`. Milestone 1 seeds one Admission In-charge per tenant. The complete persona matrix remains the specification matrix: platform/tenant/institute admins; principal; admission in-charge; data-entry, clerk, document, eligibility, assessment and conversion officers; invigilator/evaluators/interviewer; merit/selection maker-checker pairs; management viewer; applicant; guardian; student. Each must be a distinct membership/assignment in later UAT increments.

Passwords come only from runtime `UAT_TEMP_PASSWORD` or random seeder output; no password is committed. UAT data is synthetic.

## Menus and diagnostics

Menu resolution will intersect active portal menu sets with effective module, feature, permission, scope, year, assignment and workflow state. Hiding never substitutes route middleware and policy enforcement. The restricted diagnostics page currently displays safe identity, profile, tenant/scope, portal/year, roles, permissions, and enabled modules. It explicitly labels responsibility/delegation and explicit-denial data as unavailable rather than fabricating it.

## Risks

- Tenant/institute substitution: host resolution, tenant-aware membership lookup, hierarchical scope validation, and foreign-context rejection.
- Wrong profile: tenant-specific UserPersonLink and canonical tenant/person constraints.
- Stale permission/menu: role assignments and grants re-evaluated per request; caching is deferred until versioned invalidation exists.
- Direct URLs: authenticated staff routes require context, module, and effective permission middleware.
- Disabled module: both public and staff admissions routes use the tenant module gate.
- Session fixation/stale tabs: regeneration on login/context selection; context revalidation on every protected request.
- Remaining blockers: functional responsibility schemas, explicit denials, policies for admissions records, dynamic admissions menus, full UAT personas, applicant resume, operational screens and browser journeys.

## Seeders and tests

Seed order: `UatOrganisationSeeder`, `UatAcademicSeeder`, `UatWorkforceSeeder`, `UatUserSeeder`, `UatAdmissionsSeeder`, `UatApplicantSeeder`; `UatDemoSeeder` orchestrates them. Tests cover tenant-bound login, server-generated context options, hostile cross-tenant membership selection, tenant-bound profile resolution, admissions tenant isolation, route access audits, and integrity audits. Browser testing remains a certification condition because Playwright/Pest Browser is not installed and the operational vertical slice is not implemented.

## Certification

Milestone 1 can be **CERTIFIED WITH CONDITIONS** only when its migrations/seeds/tests/audits/static analysis/build pass. The complete Admissions frontend is **NOT CERTIFIED** until responsibility resolution, policies, dynamic menus, all personas, browser hostile tests, and the public-to-conversion slice pass.
