# Student and guardian profile foundation — milestone 1

## Architecture and identity boundaries

`Person` is the human identity. `StudentProfile` is the permanent tenant-level student identity and remains stable across academic years and institutes. `GuardianProfile` is a reusable tenant-level guardian identity. `StudentGuardianRelationship` is the only place where authority over a particular student is expressed.

```text
Person ──0..1 StudentProfile per tenant ──* InstituteEnrolment (later)
                                 │          └──* AcademicEnrolment (later)
                                 └──* StudentGuardianRelationship *── GuardianProfile ──1 Person
```

An institute enrolment answers where and when the student is admitted. Academic enrolment answers the year, class/section or programme/course/semester placement. Neither current academic placement nor admission numbers belong on the permanent Student Profile.

## Guardian authority matrix

| Explicit relationship flag | Intended boundary |
|---|---|
| Primary guardian | General student communication |
| Legal guardian | Legal and admission decisions |
| Financial guardian | Fees and receipts |
| Academic contact | Attendance, homework, and results |
| Emergency contact | Emergency notification |
| Pickup authorised | Pickup verification only |
| Medical consent authority | Defined medical consent |
| Residential guardian | Hostel/residence communication |

Relationship labels never imply these flags. Each authority is independently stored, dated, approved, and evaluated for one student.

## Portal authorization

```text
Student user → active student-user link → membership/portal/scope → own StudentProfile → policy
Guardian user → active guardian-user link → active dated relationship → selected StudentProfile → authority flag → feature permission → policy
```

Child selection must be server generated. A portal link alone grants no student access, and a pickup authority grants neither financial nor academic access.

## Privacy and field-level access

Later records use `public`, `internal`, `restricted`, `confidential`, and `highly_confidential` classifications. Policies combine tenant/scope, portal, relationship authority, field sensitivity, purpose, and audit requirements. Lists expose masked/minimal values; sensitive document, income, address, health, accessibility, court-order, and pickup identity access requires explicit permissions.

## Migration sequence

1. student_categories
2. student_statuses
3. guardian_occupations
4. income_bands (later profile-data milestone)
5. guardian_relationship_types
6. student_document_types and consent_types (later)
7. student_profiles
8. guardian_profiles
9. student_guardian_relationships
10. contacts, addresses, and status history
11. documents, emergency contacts, pickup, siblings, and consents
12. health and accessibility
13. portal links
14. institute-enrolment integration

## Risk controls

- Cross-tenant links: tenant global scopes, composite foreign keys, model validation, and the integrity audit.
- Duplicate identities: tenant/person and tenant/number unique constraints; later fuzzy matching never auto-merges.
- Guardian overreach: student-specific authority flags, effective dates, approval state, and policy checks.
- Sensitive-data exposure: least-privilege field policies, masking, private storage, purpose limitation, and sensitive-view audit.
- Historical loss: soft deletion and later append-only status, consent, relationship, and document-version histories.

## Operations

```bash
php artisan migrate
php artisan db:seed --class=StudentGuardianFoundationSeeder
php artisan test --group=students
php artisan erp:student-guardian-integrity-audit
composer analyse
```

The audit validates installed milestone-one records and emits warnings for intentionally deferred tables.
