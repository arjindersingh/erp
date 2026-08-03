# Shared foundation — milestone 01

## Architecture

The ERP remains a modular monolith: one Laravel runtime, one shared database, and explicit core services used by every later operational module. HTTP, API, console, queue, and scheduler entry points must establish tenant context before accessing tenant-owned data.

```text
Request / Job / Command
  -> System boot and environment health
  -> Tenant resolution and lifecycle validation
  -> Authentication and active user
  -> Membership and opaque active context
  -> Portal and scoped role assignments
  -> Enabled tenant module
  -> Effective permission
  -> Record policy
  -> Workflow / lock / segregation-of-duties rules
  -> Domain action + audit event
```

Milestone one completes the first two boundaries. `TenantResolver` maps a normalized verified domain to one tenant. `ResolvePublicTenant` activates a request-scoped `TenantContext`; `EnsureTenantIsActive` rejects suspended, inactive, maintenance, or terminated tenants. Central development domains may deliberately run without a tenant. Browser-supplied tenant IDs never participate in resolution.

## Platform relationships

```text
SystemVersion

Tenant 1---* TenantDomain
   |
   +---* TenantSubscription
   +---* Company 1---* Campus 1---* Institute
   +---* AccessScope
   +---* Person / Membership / Role / Module configuration / AuditLog

TenantContext --- exactly one resolved Tenant + optional matching TenantDomain
```

Lower-level records retain `tenant_id` in addition to company/campus/institute keys. Composite constraints in the organisation foundation prevent cross-tenant hierarchy assembly. Subscription data describes entitlement but does not replace tenant lifecycle validation.

## Access and naming standards

Permissions use lowercase `module.resource.command`; module entry uses `module.access`. Examples are `students.profile.update`, `fees.refunds.approve`, and `transport.access`. Permissions describe commands, scope describes boundaries, and policies decide whether a particular record is eligible. Role names, profile types, and menu visibility never grant access.

Protected named routes use `portal.module.resource.action`, such as `site-admin.core.health.show` or `teacher.attendance.mark`. Shared routes use `core.resource.action`; public routes use `public.resource.action`. Internal links call `route(...)`, route parameters prefer UUID/slug identifiers, and protected records still require scoped binding and policy checks.

## System health

`SystemHealthService` checks application version, database, cache, queue configuration, private storage writes, mail configuration, environment safeguards, pending migrations, PHP 8.4+, and log-directory writes. The public API returns only status, service, version, and timestamp. The detailed dashboard is protected by authentication and `core.settings.view`; failures return neutral summaries rather than secrets or exception messages.

## Migration order

For a fresh installation the relevant order is:

1. `system_versions`
2. users/framework infrastructure inherited from the Laravel skeleton
3. tenants, domains, organisation hierarchy, and access scopes
4. identity, memberships, and role assignments
5. authorization catalogue
6. modules, navigation, and audit foundations
7. tenant UUID/branding enrichment and subscriptions

The enrichment migration backfills UUIDs for pre-existing tenants/domains without deleting data. Later milestones should add active contexts and portal access before settings, workflow, files, notifications, academic years, numbering, and public content.

## Security and privilege-escalation risks

- Forged tenant IDs: ignored; resolution uses the normalized request host or explicit trusted worker context.
- Unverified/custom-domain takeover: only active, verified domain rows resolve.
- Suspended tenant bypass: lifecycle middleware runs after resolution in web and API groups.
- Context confusion: `TenantContext::activate()` rejects a domain owned by another tenant and exposes `requireTenant()` for fail-closed services.
- Cross-tenant queries: tenant-owned models expose `forTenant()`; later policies/repositories must apply it consistently rather than accepting request IDs.
- Health information disclosure: public output is minimal; detailed checks require privileged authorization.
- Health side effects: cache and storage probes use unique temporary keys/files and clean them immediately.
- Subscription confusion: service entitlement and tenant status are separate; a subscription cannot silently reactivate a tenant.
- Sequential identifier enumeration: tenant, domain, subscription, and system-version records expose UUIDs.

## Files, commands, and expected result

- `app/Core/System`: typed health results, version records, and the system health service.
- `app/Core/Tenancy`: lifecycle/domain/subscription enums and models, resolver, and request-scoped context.
- `app/Http/Middleware`: resolution and active-tenant enforcement.
- `database/migrations/0000_00_00_000000_create_system_versions_table.php`: deploy/version history.
- `database/migrations/2026_08_03_076000_complete_tenant_foundation.php`: UUID, branding, domain lifecycle, and subscriptions.
- `database/factories` and `database/seeders`: deterministic foundation test/demo records.
- `tests/Feature/Core/SystemBootTenantFoundationTest.php`: health, resolution, lifecycle, context, UUID, and isolation boundaries.

Run `php artisan migrate --force`, `php artisan db:seed --force`, `php artisan test --compact`, `vendor/bin/phpstan analyse`, and `php artisan route:list`. Expected result: no pending migrations, a healthy or warning-only local health report, verified active domains resolve, unavailable tenants fail, cross-tenant context activation throws, tests and static analysis pass, and protected health diagnostics have a unique named route.

## Suggested commit sequence

Milestone one is intentionally committed atomically as `feat(foundation): complete system boot and tenant context` after all prescribed verification passes. Subsequent commits should follow the master implementation sequence and must not begin operational modules before shared foundation readiness.
