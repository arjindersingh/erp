# Dynamic access architecture

## Security boundary

Authorization is evaluated for a resolved active context, never from a profile, designation, membership type, role name, menu item, or browser-supplied identifier. The eventual `PermissionResolver` is the single command-level decision point; policies remain authoritative for records and workflow state.

```text
Resolved tenant
  -> authenticated active user
    -> active membership
      -> active scope
        -> active portal
          -> active scoped role assignments
            -> effective role grants and user overrides
              -> enabled tenant module
                -> record policy and workflow/SoD rule
```

An explicit deny outranks an explicit allow, and an explicit allow outranks a role grant. None of them may bypass tenant isolation, an inactive identity or membership, a disabled module, an invalid scope/portal, a record policy, workflow locking, or mandatory segregation of duties.

## Relationship diagram

```text
Tenant 1---* Role *---* Permission *---1 Module
             |       role_permissions      |
             |                         0..1 ModuleFeature
             |
User 1---* RoleAssignment *---1 UserMembership
             |                         |
             +---1 AccessScope <-------+
             |
             +---0..1 Portal            (next milestone)

Tenant *---* Module through TenantModule (next milestone)
User   *---* Permission through UserPermissionOverride (next milestone)
```

System roles have a null `tenant_id`; tenant-custom roles carry their owning tenant. Role assignments carry tenant, membership, user, and scope together and are validated server-side. A permission belongs to exactly one module and optionally one feature. `role_permissions` records grant provenance, status, and expiry rather than acting as an unqualified pivot.

## Permission standard

Commands use lowercase `module.resource.command`, with letters, digits, and underscores in each segment. Module entry is the intentional two-segment exception `module.access`. Examples: `transport.routes.update`, `fees.refunds.approve`, and `transport.access`. Vague capabilities such as `manage_students` are rejected by the model before persistence. Permission `name` is kept equal to `code` for Spatie compatibility.

## Effective resolution

The resolver milestone will evaluate, in order: tenant status, tenant-module status, user status, membership validity, role-assignment dates/status, scope containment, portal mapping, explicit deny, explicit allow, effective non-expired role grants, record scope, policy, then workflow and segregation-of-duties constraints. Multiple active roles contribute a union of grants. Every query is constrained by the trusted active context, not request IDs.

## Migration order

1. Laravel users, cache, and jobs.
2. Spatie base permission tables (configured to use `role_permissions`).
3. Sanctum and observability tables.
4. `tenants`, companies, campuses, institutes, and `access_scopes`.
5. persons, identities, memberships, and scoped `role_assignments`.
6. `2026_08_03_073000_create_dynamic_access_foundation.php`: modules, features, enriched permissions and roles, and auditable role grants.

Later migrations must follow this foundation: portals and tenant modules; scoped user overrides; dependencies/conflicts; permission versions; menus; approval and audit records.

## Privilege-escalation risks

- Cross-tenant ID substitution: validate role, membership, user, and scope tenant identity together and use composite foreign keys where foundations support them.
- Global Spatie checks: never treat `HasRoles` alone as the ERP authorization decision; scoped assignments and active context are mandatory.
- Tenant role mutation: query tenant-owned roles through tenant constraints and keep protected system roles non-editable/non-deletable.
- Guard confusion: reject grants where role and permission guards differ.
- Stale or expired access: check dates on every request; version caches and revoke stale contexts in subsequent milestones.
- Hidden-action submission: enforce middleware plus policies in handlers; navigation is presentation only.
- Self-approved elevation and toxic combinations: add approval and segregation-of-duties services before exposing access administration.
- User allow misuse: user overrides remain exceptional and cannot bypass module, scope, policy, workflow, or tenant controls.
- Permission enumeration: expose UUID route keys and return tenant-safe 403/404 responses without sensitive diagnostics.

## Foundation files and operation

- `app/Core/Authorization`: access enums, custom Spatie-compatible Role and Permission models, grant model, validation, and relationships.
- `app/Core/Modules`: Module and ModuleFeature catalogue models.
- `database/migrations/2026_08_03_073000_create_dynamic_access_foundation.php`: schema extension; runs after tenant and membership foundations.
- `database/factories`: factories for each foundation model and grant.
- `database/seeders`: core modules/features, initial permission catalogue, protected system roles, and Transport proof grants.
- `tests/Feature/Core/DynamicAccessFoundationTest.php`: naming, uniqueness, tenant role boundaries, multiple grants, role protection, and idempotent catalogue tests.

Install or rebuild with `php artisan migrate --seed`. Run the milestone tests with `php artisan test --compact tests/Feature/Core/DynamicAccessFoundationTest.php`, or the complete suite with `php artisan test --compact`. Expected result: schema migrates, seeders can run repeatedly, the Transport module is shared through distinct role grants, invalid permissions fail before persistence, duplicate permission codes fail at the database, and protected roles cannot be deleted.

## Suggested commit sequence

1. `feat(access): add permission and role domain enums`
2. `feat(access): add module and permission catalogue schema`
3. `feat(access): add custom role permission models and factories`
4. `feat(access): seed core catalogue and transport role grants`
5. `test(access): cover dynamic access foundation boundaries`
6. `docs(access): document architecture and threat model`
