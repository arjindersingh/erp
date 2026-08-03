# Memberships and Active Scope

## Requirement Review

Membership records every valid organisational relationship assigned to a user.
It is durable authorization input, not mutable session state. A future
`UserActiveContext` will record the one validated membership, role assignment,
portal, scope, and academic year selected for a session.

This milestone implements the durable membership and hierarchical-scope side of
that boundary. It intentionally does not add active-context persistence, portal
tables, request middleware, or switching UI yet.

## Final Architecture

```text
Tenant
└── AccessScope: Tenant
    └── AccessScope: Company
        └── AccessScope: Campus
            └── AccessScope: Institute

User
└── UserMembership (where the user belongs)
    ├── Person/Profile
    ├── AccessScope
    └── RoleAssignments (capacity in which the user may act)
        ├── Spatie Role -> Permissions
        └── AccessScope equal to or below the membership scope

Authenticated Session (next milestone)
└── UserActiveContext (one selected working context)
```

Membership, role, permission, and active context remain separate:

- Membership answers where and in what relationship the user belongs.
- Role assignment answers in what capacity the user may act within that boundary.
- Permission answers which exact operation may be performed.
- Active context answers which valid combination the session is currently using.

## Scope Inheritance

| Assigned scope | Tenant records | Companies | Campuses | Institutes |
| --- | --- | --- | --- | --- |
| Tenant | Yes | All in tenant | All in tenant | All in tenant |
| Company | No | Assigned company | Its campuses | Its institutes |
| Campus | No | No | Assigned campus | Its institutes |
| Institute | No | No | No | Assigned institute only |

The materialized `path` enables `containsScope()`, `isAncestorOf()`, and
`isDescendantOf()` without recursive queries. `ScopeHierarchyValidator` generates
the path and rejects missing IDs, wrong organisation combinations, skipped parent
levels, cross-tenant parents, inactive parents, and unsafe re-parenting.

`ScopeAccessService` obtains boundaries only from selectable server-side
memberships. It never grants access because a company, campus, institute, or scope
ID was submitted by a browser.

## Migration Order

1. `2026_08_03_071000_create_tenant_organization_tables.php`
   creates the organisation hierarchy and hierarchical `access_scopes`.
2. `2026_08_03_072000_create_person_user_identity_tables.php`
   creates identities, `user_memberships`, and the role-assignment interface.
3. Next milestone migrations will add portals, portal access, active contexts,
   context options, context-switch history, and academic-year references.

The scope table includes tenant-bound composite foreign keys for company, campus,
institute, and parent scope. Memberships have a tenant-bound foreign key to scope.
An `active_identity_key` unique index prevents concurrent duplicate active
memberships for the same tenant, user, membership type, and scope while preserving
inactive history.

## Implementation Files

| File | Purpose | Depends on |
| --- | --- | --- |
| `app/Core/Authorization/ScopeType.php` | Scope values, hierarchy levels, labels, colours | PHP enums |
| `app/Core/Identity/MembershipType.php` | Supported organisational relationships | PHP enums |
| `app/Core/Identity/MembershipStatus.php` | Membership lifecycle and selectable status | PHP enums |
| `app/Core/Authorization/RoleAssignmentStatus.php` | Role-assignment lifecycle | PHP enums |
| `app/Core/Authorization/AccessScope.php` | Hierarchy relationships and containment methods | Tenant and organisation models |
| `app/Core/Authorization/ScopeHierarchyValidator.php` | Validates and materializes hierarchy paths | AccessScope and organisation models |
| `app/Core/Authorization/ScopeAccessService.php` | Resolves user boundary from selectable memberships | UserMembership |
| `app/Core/Identity/UserMembership.php` | Lifecycle, date, tenant, user, profile, scope, and role relations | Identity foundation |
| `app/Core/Identity/MembershipValidator.php` | Same-tenant identity checks and duplicate-active key | User links, profiles, scopes |
| `app/Core/Authorization/RoleAssignment.php` | Membership-bound role and descendant scope interface | Spatie Permission |
| `database/factories/*Factory.php` | Consistent tenant hierarchy and membership test data | Eloquent factories |
| `tests/Feature/Core/MembershipScopeFoundationTest.php` | Hierarchy, lifecycle, assignment, and isolation checks | Pest/PHPUnit |

## Security Risks

| Risk | Current control | Next milestone control |
| --- | --- | --- |
| Forged organisation IDs | Server-side validators and composite tenant FKs | Form Requests and policies |
| Cross-tenant parent scope | Composite parent FK and hierarchy validator | Audit suspicious attempts |
| Scope escalation | Assignment scope must be membership scope or descendant | Active-context validation middleware |
| Duplicate active membership race | Database unique active identity key | Transactional membership actions |
| Expired or suspended relationship | `selectable()` status/date checks | Immediate active-context revocation |
| Stale session context | Not yet stored | Database-backed context plus session binding |
| Higher scope mistaken for permission | Scope provides boundary only | Scoped permission resolver and policy checks |
| Browser-supplied combinations | No selection endpoint in this milestone | Opaque server-generated context option UUIDs |

## Verification

```bash
php artisan migrate
php artisan test tests/Feature/Core/MembershipScopeFoundationTest.php
php artisan test
composer analyse
```

Expected result: hierarchy and membership tests pass, cross-tenant and invalid
hierarchy attempts throw domain exceptions, duplicate active memberships fail at
the database constraint, and Larastan reports no errors.

Suggested commit sequence:

1. `feat(auth): add hierarchical organisational scopes`
2. `feat(auth): enforce membership lifecycle and tenant ownership`
3. `feat(auth): constrain role assignments to membership scopes`
4. `test(auth): cover membership and scope isolation`
5. `docs(auth): document memberships and active scope`

The next milestone starts with portal definitions and `user_portal_access`, then
adds database-backed `user_active_contexts`, context-switch history,
`AvailableContextBuilder`, and the central `ActiveContext` service.
