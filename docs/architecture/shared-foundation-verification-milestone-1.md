# Shared Foundation Verification Report — Milestone 1

Test date: 2026-08-04
Baseline commit: `a3ed0e9`

## A. Executive summary

Overall status: **Milestone 1 passed; full foundation NOT CERTIFIED**.

The tenant-resolution and organisation-isolation milestone now has executable proof for domain resolution, unknown/unverified/inactive tenant rejection, browser tenant-ID rejection, tenant query scopes, cross-tenant URL substitution, cross-tenant parent relationships, and tenant-bound uniqueness. Nine tests with 20 assertions pass. The complete foundation cannot be certified until the later authentication, context, authorization, module, navigation, settings, workflow, audit, file, notification, academic-year, queue, cache, and API milestones pass.

One high-risk architectural defect was found and remediated: organisation queries were not automatically tenant-scoped. One critical relationship-integrity defect was found and remediated: the database allowed campus/institute parent IDs to cross tenant boundaries. No known critical or high finding remains in the tested milestone scope.

Certification recommendation: continue foundation verification; do not begin operational modules.

## B. Environment

| Item | Value |
| --- | --- |
| Laravel | 13.23.0 |
| PHP | 8.4.15 |
| Database | SQLite test database |
| Cache | Test configuration |
| Queue | Test configuration |
| Testing framework | PHPUnit 12 / Pest 4 |
| Baseline | `a3ed0e9` |
| Test date | 2026-08-04 |

## C. Verification architecture and strategy

Requests resolve a verified host through `TenantResolver`; `ResolvePublicTenant` activates the request-scoped `TenantContext`; `EnsureTenantIsActive` blocks unavailable tenants. Tenant-owned Eloquent models use `BelongsToTenant`, which installs `TenantScope` and reads only the central context. Parent ownership is enforced again at the database boundary through composite foreign keys.

Each milestone follows: encode an attack or invariant as a failing test, run it, correct the narrow root cause, rerun the focused group, run the complete regression suite, format, and commit. Later milestones must add reusable model-operation contracts for list/search/view/create/update/delete/restore/approve/export/download/print/reference/API/queue.

## D. Deterministic two-tenant model

```text
Tenant A (a.erp.test)
└── Company A
    └── Campus A
        ├── Institute A1
        └── Institute A2

Tenant B (b.erp.test)
└── Company B
    └── Campus B
        ├── Institute B1
        └── Institute B2
```

Factories create fixed labels/codes in security tests. Requests derive the tenant from the host; query parameters and route identifiers never select tenant context.

## E. Milestone isolation matrix

| Attack/invariant | Expected | Observed | Status |
| --- | --- | --- | --- |
| Tenant B ID submitted on Tenant A host | Tenant A remains active | Tenant A resolved | PASS |
| Unknown host | 404 | 404 | PASS |
| Unverified domain | 404 | 404 | PASS |
| Suspended tenant | 403 | 403 | PASS |
| List companies in Tenant A context | No Tenant B rows | Only Company A | PASS |
| Find Tenant B company by internal ID | Null/404 | Null/404 | PASS |
| Find Tenant B company by slug | 404 | 404 | PASS |
| Tenant A campus references Tenant B company | Database rejection | Query exception | PASS |
| Tenant A institute references Tenant B campus | Database rejection | Query exception | PASS |
| Reuse company code in another tenant | Allowed | Allowed | PASS |
| Switch A context to B | No stale A query scope | Only Company B | PASS |

Operations not shown here remain unverified and therefore cannot be inferred as passing.

## F. Test suites and file paths

| Suite | File | Purpose | Command |
| --- | --- | --- | --- |
| Tenant resolution | `tests/Feature/Foundation/Tenancy/TenantResolutionTest.php` | Host resolution, status, forged tenant input | `php artisan test --group=tenancy` |
| Organisation | `tests/Feature/Foundation/Organisation/OrganisationHierarchyIsolationTest.php` | Composite ownership and uniqueness | `php artisan test --group=foundation` |
| Query scope | `tests/Feature/Foundation/Isolation/TenantQueryScopeTest.php` | Automatic scope and context switching | `php artisan test --group=isolation` |
| Direct URL | `tests/Feature/Foundation/Isolation/CrossTenantUrlTest.php` | Internal-ID and slug substitution | `php artisan test --group=security` |
| Ownership audit | `tests/Feature/Foundation/Tenancy/DataOwnershipAuditCommandTest.php` | Audit command contract | `php artisan test --group=tenancy` |

Planned suites retain the requested structure under `tests/Feature/Foundation/{Authentication,Memberships,ActiveContext,Portals,Authorization,Modules,Navigation,Settings,Workflow,Audit,Files,Notifications,AcademicYear,Queues,Api}` and `tests/Unit/Foundation`.

## G. Component results

| Component | Tests | Passed | Failed | Status |
| --- | ---: | ---: | ---: | --- |
| Tenant resolution | 3 | 3 | 0 | PASS (milestone scope) |
| Organisation hierarchy | 3 | 3 | 0 | PASS (milestone scope) |
| Tenant query isolation | 2 | 2 | 0 | PASS (milestone scope) |
| Cross-tenant URL | 1 | 1 | 0 | PASS (milestone scope) |
| Remaining foundation components | 0 | 0 | 0 | NOT VERIFIED |

## H. Findings and remediation

### Remediated — High: missing automatic organisation tenant scope

Actual behaviour before remediation: `Company::query()`, `Campus::query()`, and `Institute::query()` could return all tenants unless each caller remembered `forTenant()`. Impact: a missed filter could expose cross-tenant records. Root cause: the existing `BelongsToTenant` trait only defined a relationship and local scope. Fix: install a context-driven `TenantScope` on all three models.

### Remediated — Critical: cross-tenant organisation parent references

Actual behaviour before remediation: simple foreign keys proved that parent IDs existed but not that parent and child shared a tenant. Impact: corrupted ownership graphs and possible cross-tenant reference/link access. Root cause: missing composite ownership foreign keys. Fix: enforce `(tenant_id, company_id)` and `(tenant_id, company_id, campus_id)` relationships in the schema.

### Warning: audit breadth is intentionally milestone-limited

`erp:data-ownership-audit` currently validates registered organisation models for `tenant_id`, tenant-leading indexes, and `TenantScope`. Unsafe raw queries, cache/storage/job namespaces, and all other tenant-owned models must be added as those components are implemented and catalogued.

Highest remaining risk areas are forged active contexts, role/permission cache separation, cross-tenant files and signed URLs, queue context reconstruction/cleanup, settings cache inheritance, API scope validation, and audit masking/integrity.

## I. Executed proof

```text
php artisan migrate:fresh --seed --env=testing
PASS — all migrations and seeders completed

php artisan test --group=foundation
PASS — 9 tests, 20 assertions

php artisan test
PASS — 64 tests, 184 assertions

php artisan erp:data-ownership-audit
PASS — 9 ownership checks

vendor/bin/pint --test <milestone files>
PASS
```

The repository-wide Pint run still reports pre-existing style findings outside this milestone in configuration, browser-test, and bootstrap files. This blocks final certification but did not justify modifying unrelated files in this commit.

## J. Certification decision

**NOT CERTIFIED**

Milestone 1 passes, but the mandatory full-system isolation matrix and all remaining component interactions have not yet been executed. This report makes no security claim beyond the tested operations above.

## K. Commit sequence

1. `test(foundation): certify tenant isolation milestone one`
2. Future commits: authentication/context; authorization/modules/navigation; settings/workflow/audit; files/notifications/year; queues/cache/API; final static/performance/security certification.
