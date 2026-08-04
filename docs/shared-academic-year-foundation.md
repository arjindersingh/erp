# Shared academic-year foundation

The shared foundation owns academic-year boundaries and lifecycle; downstream academic modules reference it rather than creating their own year fields.

## Resolution and access

- Years may be defined at tenant, company, campus, or institute level. The most specific current default wins.
- Every lookup filters `tenant_id` explicitly, including jobs and tests where no request tenant scope exists.
- Optional scope assignments restrict a year to selected access-scope subtrees. Without assignments, the year boundary itself controls availability.
- UI selectors must submit the opaque encrypted value produced by `SelectAcademicYearAction::optionId()`. Selection revalidates the active tenant, user membership, scope, year lifecycle, and assignment server-side.
- `AcademicYearContext` is request-scoped. Routes requiring it use `academic-year` middleware.

## Locks

Locked, closed, and archived years remain selectable for historical reporting but are read-only. Granular locks can additionally target an access-scope subtree, module key, and resource type. Mutation routes use `academic-year.writable[:module,resource]`; blocked writes return HTTP 423.

Assignments and locks are tenant-owned, time-bounded, auditable records. Releasing a lock preserves its history through `released_at` and `released_by`.
