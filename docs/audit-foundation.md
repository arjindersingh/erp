# Audit foundation architecture

## Purpose and boundaries

The ERP audit subsystem records durable business and security evidence. It is intentionally separate from Spatie's generic `activity_log` table and from Laravel application logs.

- Application logs diagnose exceptions, failed jobs, integrations, and performance problems. They may contain stack traces and operational detail and are managed by engineering operations.
- Audit logs answer who did what, in which tenant and active context, to which subject, when, why, and with what outcome. They are append-only compliance and investigation records.
- Activity timelines are permission-filtered projections of relevant audit events for a particular business record. They are not a separate source of truth.
- Security events are classified audit records with elevated severity, alert potential, stronger retention, and privacy controls.

## Relationships

```text
AuditEventDefinition 1---* AuditLog 1---* AuditLogChange
                              |
                              +---0..1 Tenant / Company / Campus / Institute
                              +---0..1 User / Person / Membership / AccessScope
                              +---0..1 RoleAssignment / Role / Portal
                              +---0..1 Module / ModuleFeature
                              +---0..1 polymorphic Subject
```

`AuditContext` is an immutable snapshot used during creation; it is not an editable database record. `AuditContextFactory` builds trusted common context from tenant resolution, authentication, request attributes populated by future active-context middleware, and the current execution source. Controllers do not supply actor or tenant IDs from browser data.

## Event naming

Event codes are stable lowercase dot-separated business identifiers:

```text
domain.resource.action
auth.login.succeeded
access.role.assigned
fees.receipt.cancelled
examination.marks.corrected
security.cross_tenant.denied
```

The definition catalogue maps each event code to category, standard action, default severity, safe display title, security/sensitivity flags, and future retention or notification policies. The database stores `action` as a string so modules can add well-named actions without changing the shared enum; `AuditAction` remains the preferred common catalogue. Definitions contain data templates only, never executable PHP.

## Sensitive-data treatment

`SensitiveFieldRegistry` runs before audit persistence. Passwords, tokens, secrets, recovery codes, and private keys are excluded recursively. Configured personal, medical, bank, email, and telephone values are masked in both JSON snapshots and normalized changes. Government identifiers can be represented as hash-only values. The logger never captures request bodies automatically, strips query strings from stored URLs, and does not trust forwarded-IP form input.

Masking protects stored evidence; later viewer policies must still require `audit.logs.view_sensitive` before exposing any specially protected values or hashes.

## Transaction-safe creation and integrity

`AuditLogger::change()` computes meaningful changed fields, removes excluded/no-op fields, and writes the parent event and normalized field rows in one database transaction. Business services should call it inside the same transaction as successful critical state changes so neither the business mutation nor its success audit can commit alone. Technical failure audits should be written in a separate transaction after rollback and marked `failed`.

Each tenant's records receive a SHA-256 link to the preceding record under a row lock. This provides early tamper evidence, not absolute immutability. A later milestone must add verification checkpoints, a verification command, restricted database credentials, external immutable storage/outbox delivery, and alerting on chain failures.

## Migration order

1. Tenant, identity, membership, scope, authorization, module, and portal foundations.
2. `2026_08_03_075000_create_audit_foundation.php`.
3. Future retention/legal-hold tables.
4. Future alert, export, outbox, archive, and integrity-checkpoint tables.

The migration creates definitions first, then append-only audit logs, then searchable normalized changes. Audit logs intentionally have `created_at` but no ordinary `updated_at` or soft-delete column.

## Risks and controls

- Cross-tenant attribution: the logger rejects subjects whose tenant conflicts with trusted context and database foreign keys restrict referenced entities.
- Secret leakage: recursive exclusion and masking run before JSON and change-row creation; request bodies are never captured by default.
- History rewriting: Eloquent update/delete operations throw, audit records have no ordinary update timestamp, and hashes link records. Database-level least privilege remains required for production.
- False success evidence: critical services must create success audits inside their business transaction.
- Hash-chain contention: the per-tenant chain is correct for the foundation but high-volume deployments may move to partitioned chains/outbox serialization.
- Storage growth: selective events, optional JSON, normalized searchable fields, composite indexes, expiry dates, and future retention/archive policies limit operational impact.
- Search privacy: list views should avoid selecting large JSON and must use tenant/scope policies; sensitive content must never become an unrestricted search field.
- Audit outage: required synchronous events should fail closed where regulation demands it; reliable external delivery will use the future outbox.

## Foundation files

- `app/Core/Audit`: enums, immutable context and actor DTOs, context factory, models, append-only concern, sensitive-field registry, and central logger.
- `database/migrations/2026_08_03_075000_create_audit_foundation.php`: indexed catalogue, event, and normalized-change storage.
- `database/seeders/AuditFoundationSeeder.php`: eleven required demonstration definitions.
- `database/factories/Audit*Factory.php`: event, log, and change test data.
- `tests/Feature/Core/AuditFoundationTest.php`: context, subject, masking, cross-tenant rejection, append-only, hash-chain, and catalogue tests.

Install with `php artisan migrate --seed`. Run `php artisan test --compact tests/Feature/Core/AuditFoundationTest.php` for this milestone or `php artisan test --compact` for the complete suite. Expected result: required definitions seed idempotently, meaningful events retain trusted context, secrets never persist, changed fields are searchable, cross-tenant subjects fail, and history cannot be changed through the models.

## Suggested commits

1. `feat(audit): define audit taxonomy and immutable context`
2. `feat(audit): add append-only event and change schema`
3. `feat(audit): add sensitive field sanitization`
4. `feat(audit): add transaction-safe central logger`
5. `database: seed core audit definitions and permissions`
6. `test(audit): cover isolation masking and integrity chain`
7. `docs(audit): document compliance architecture`
