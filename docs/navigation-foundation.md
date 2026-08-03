# Module and navigation foundation

## Architecture

Modules and features describe what the ERP can do. Portals describe the interface boundary in which a user is operating. Menu sets describe a portal-specific navigation arrangement. Menu items are links and groups inside that arrangement. Tenant-module records control whether a capability is available to a tenant; a menu item never activates a module or grants a permission.

```text
Module 1---* ModuleFeatureGroup 1---* ModuleFeature
   |                                      |
   +---* TenantModule *---1 Tenant        |
   |                                      |
   +-------------------* MenuItem *--------+
                              |
Portal 1---* MenuSet 1---------+
                 |
                 +---* MenuItem (self-referencing tree, maximum 3 levels)
```

The future resolver layer completes the runtime path:

```text
ActiveContext
  -> portal-specific MenuSet
  -> enabled and effective TenantModule
  -> active Module and ModuleFeature
  -> central effective-permission decision
  -> scope and academic-year compatibility
  -> user display preferences
  -> prune empty groups
  -> MenuTree
```

The same Transport module therefore produces different navigation without duplicating business functionality. A Staff menu exposes fleet, allocations, tracking, and reports; an Administration menu emphasizes review and oversight; a Parent menu exposes linked-child routes and complaints; a Student menu exposes own-route navigation. The later `MenuBuilder` will filter these candidates through the central permission resolver and active context.

## Separation of concerns

- `Module`: stable functional boundary, route namespace, scope capabilities, lifecycle, and presentation defaults.
- `ModuleFeatureGroup`: administrative and UX grouping only; it has no authorization power.
- `ModuleFeature`: navigable capability metadata such as route, feature type, and whether search/favourites are appropriate.
- `TenantModule`: tenant-specific activation dates and harmless presentation/configuration overrides. It cannot contain executable business logic.
- `Portal`: authenticated or public interface boundary.
- `MenuSet`: system template or tenant-specific arrangement for exactly one portal and menu form.
- `MenuItem`: navigation candidate with route or safe external URL, permission metadata, ordering, and a shallow validated tree.

## Resolution order

Runtime navigation must check: active menu set, active item, matching portal, effective tenant-module activation, active feature, valid active context, supported organisational scope, academic-year requirement, central permission conditions, tenant visibility conditions, user UI preferences, and finally visible descendants. Direct routes independently retain module middleware, permission middleware, scope checks, policies, and workflow authorization.

## UX principles

Keep menus task-oriented, shallow, searchable, keyboard operable, and specific to the current portal and scope. Prefer seven to nine top-level choices, familiar nouns for destinations, verbs for quick actions, labelled icons, visible focus, `aria-current`, `aria-expanded`, large touch targets, and explicit context indicators. Favourites and preferences reorganize visible items only; they never change effective access.

## Risks addressed in this milestone

- Cross-module feature groups are rejected.
- Menu parents must be groups in the same menu set.
- Circular trees and depths beyond three displayed levels are rejected.
- Menu permission codes must exist in the authorization catalogue.
- Feature-linked items must use a feature from the selected module.
- External URLs accept only valid HTTP(S) destinations; route and external URL cannot coexist.
- Tenant-module configuration is unique per tenant/module and core modules cannot be disabled.
- Tenant-specific module settings remain isolated.

Later milestones must add subscription entitlement and dependency checks, central menu resolution, user preferences/favourites/recents, permission-condition pivots, cache versioning, route validation, admin preview/audit, middleware, and accessible Livewire/Filament/API presentation.

## Migration order and operation

`2026_08_03_074000_create_navigation_foundation.php` runs after the authorization catalogue migration. It enriches modules/features, then creates feature groups, tenant module configuration, portals, menu sets, and menu items. Future migrations should add module dependencies and menu permission conditions before role mappings and personalisation tables.

Run `php artisan migrate --seed` to install the catalogue and demonstration menus. Run `php artisan test --compact tests/Feature/Core/NavigationFoundationTest.php` for this milestone or `php artisan test --compact` for the full suite. Expected result: nine portals and differentiated Transport menu templates are seeded idempotently, tenant settings remain isolated, and invalid navigation structures fail before persistence.

## Suggested commit sequence

1. `feat(modules): enrich module and feature metadata`
2. `feat(navigation): add portals menu sets and menu items`
3. `feat(navigation): validate safe shallow menu hierarchies`
4. `database: seed portal-specific transport navigation`
5. `test(navigation): cover tenant and hierarchy boundaries`
6. `docs(navigation): document architecture and UX rules`
