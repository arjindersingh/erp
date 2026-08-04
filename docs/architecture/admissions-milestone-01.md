# Admissions architecture and Milestone 1

## Architectural decision

Admissions is a bounded module over the certified tenant, organisation, academic, identity, file, workflow, audit, notification, and number-series foundations. Application records are pre-enrolment snapshots, never substitute `Person`, `StudentProfile`, `GuardianProfile`, class, programme, or enrolment masters. Every staff query is tenant scoped; institute scope is an additional authorization boundary. Public requests resolve the tenant from the host before route-model binding.

The lifecycle is: campaign and offering configuration → public/manual/assisted/import intake → immutable submission snapshot → completeness and document scrutiny → eligibility → dependency-aware assessment components → frozen/versioned scoring → provisional/final merit and objections → transaction-safe seat selection → offer/response/upgrade → final verification → idempotent canonical student/guardian conversion and enrolment.

## Intake flows

| Mode | Actor and authentication | Provenance | Controls |
|---|---|---|---|
| Public online | Candidate; no ERP account to start | `public_online`, domain, IP, user agent | open window, throttling, secure token/OTP, duplicate fingerprint |
| Manual paper | Authorized admissions clerk | `manual_paper`, serial, receiver, received time, data-entry user, private scan | segregation of entry and verification; same workflow validation |
| Assisted | Candidate owns data; staff acts on their behalf | channel, counter, actor, reason, confirmation method/time | scoped permission, candidate confirmation, audit |
| Import | Authorized import operator and approved batch | `bulk_import`, batch, source reference, mapping version | preview, reference validation, duplicate report, transactional rows, reversible batch |

Public access does not disclose whether an applicant exists. Resume will compare a SHA-256 token hash in constant time or use expiring OTP/signed links. Raw access tokens are returned once and never persisted.

## Actor matrix

| Actor | May do | Must not do |
|---|---|---|
| Candidate/applicant | start, resume, edit own draft, submit, track, respond to offer | enumerate campaigns outside tenant or other applications |
| Data-entry clerk | receive and enter assigned paper/assisted forms | verify own entry when separation is enabled |
| Document/eligibility verifier | inspect assigned records and record reasoned outcomes | edit applicant claims silently or cross institute scope |
| Evaluator/panel member | view assigned candidates and score assigned criteria | view unassigned candidates; approve formula/selection |
| Merit operator/approver | simulate/freeze/generate or independently approve/publish | self-approve where segregation is enabled |
| Selection/seat officer | simulate and allocate under approved plan | over-allocate or mutate published merit |
| Final verifier/converter | verify offer conditions and execute conversion | alter selection or create duplicate canonical identities |
| Tenant auditor | read scoped timelines and integrity results | mutate operational records |

Authorization uses permission codes plus active tenant/company/campus/institute/academic-year scope and assignment; role names have no special behavior.

## Configurable scrutiny and scoring

`ScrutinyPlan` owns ordered components and dependency edges. Components declare type, raw maximum, qualifier, weight, mandatory/eliminating flags, applicability, evaluation/aggregation/normalization/rounding rules, evaluator count, absence/retest rules, and approval requirement. A dependency edge has predecessor, successor, minimum predecessor outcome, and whether failure blocks or rejects. A DAG validation prevents cycles. Parallel branches become eligible together; mandatory leaves must complete before scoring.

Examples:

- One component: previous examination marks, maximum 100, weight 100%, mandatory.
- Two components: normalized previous marks 70% plus panel interview 30%; both mandatory, interview may depend on eligibility.
- All-component example: previous marks 25%, aptitude 20%, written 25%, interview 20%, portfolio 10%; document and eligibility components are eliminating gates with zero merit weight.

Each evaluator submission remains immutable by version. Component aggregation creates an approved component result. For component `i`, `normalized_i = normalization(raw_i, maximum_i)` and `weighted_i = normalized_i × weight_i / 100`. Total merit is the rounded sum plus versioned bonuses minus penalties, subject to component and total qualifiers. A score calculation stores raw, normalized and weighted values, rule inputs, qualifier outcomes, formula version, precision, and checksum. Published formula versions are immutable; changes fork a new version.

Tie rules are ordered and versioned. Each applied value and decision is stored. A random draw requires a persisted cryptographic seed, candidate set hash, algorithm, actor, and timestamp. Rank is deterministic for the same frozen inputs and formula version.

## Seats and selection

Seat matrices are versioned by campaign/offering/category/quota/round and store authorized capacity plus derived offered/accepted/confirmed/cancelled counts. Legal percentages are data, not code. Vertical/horizontal reservation, conversion, carry-forward, expiry, and supernumerary behavior belong to an approved selection plan.

Selection sorts eligible candidates by final rank and deterministic tie values, then walks each candidate's ranked choices against applicable quota pools. A database transaction locks candidate rows and relevant seat rows (`FOR UPDATE`), records an allocation ledger entry, and increments offered seats only after constraints pass. Simulation writes no operational allocation. Publication freezes the selection run; offers refer to its version. Decline, expiry, failed verification, withdrawal, or confirmed upgrade releases the old ledger reservation exactly once. Waitlist rank is independent of merit rank. Float preserves the held seat until an upgrade transaction succeeds.

## Relationship diagram

```text
Tenant ─ Company ─ Campus ─ Institute ─ AcademicYear
  │                                  │
  └──────── AdmissionCampaign ───────┘
                 │
                 ├─ CampaignOffering ── AcademicClass | ProgrammeOffering
                 ├─ FormDefinition ─ Sections ─ Fields
                 ├─ DocumentRequirement
                 ├─ ScrutinyPlan ─ Components ─ Dependencies
                 │                    ├─ Assessments/Schedules
                 │                    └─ Evaluations ─ ComponentResults
                 ├─ FormulaVersion ─ TieRules
                 ├─ SeatMatrix ─ Category/Quota
                 └─ SelectionPlan ─ Rounds ─ Runs
                 │
          AdmissionApplication
            ├─ Guardians / Choices / Qualifications / SubjectMarks / Documents
            ├─ EligibilityResults / Schedules / ComponentResults
            ├─ ScoreCalculation ─ MeritEntry ─ Objections
            ├─ SelectionEntry ─ WaitlistEntry ─ Offer ─ Responses
            └─ FinalVerification ─ ConversionRecord
                                      ├─ Person → StudentProfile
                                      ├─ Person → GuardianProfile(s)
                                      └─ InstituteEnrolment → AcademicEnrolment
```

## Migration execution order

1. Campaigns, campaign offerings, application shell and source tracking (Milestone 1).
2. Form definitions, sections, fields, snapshots, guardians, choices, qualifications, subject marks, document requirements/documents.
3. Scrutiny plans, components, dependency DAG, eligibility rule sets/rules/results.
4. Assessments, venues, schedules, attendance, evaluator assignments, interview panels/members, evaluation scores, component results.
5. Formula versions, calculations, tie rules, merit runs/entries and objections.
6. Seat categories/matrices, quota rules, selection plans/rounds/runs/entries, waitlists.
7. Offers/responses, final verification, status histories, notes, conversion records and import batches.

Splitting by dependency boundary keeps rollback safe and permits PostgreSQL/MySQL-compatible foreign-key creation.

## Risk register and controls

- Security: tenant spoofing, IDOR, token theft, enumeration, malicious uploads, bot traffic, and evaluator overreach. Resolve host server-side, ignore browser organisation IDs, scope policies and files, hash/rotate tokens, generic responses, throttle/CAPTCHA hooks, malware scanning, signed downloads, assignment scopes, and comprehensive audit.
- Duplicate identity: spelling/transliteration changes, shared contacts, twins, legacy IDs, and guardian reuse can cause false matches. Use normalized multi-attribute fingerprints only to flag; resolve through a permissioned identity-match workflow; canonical conversion has tenant/person uniqueness and idempotency keys.
- Scoring: drifting formulas, precision differences, missing results, evaluator collusion, and retroactive edits. Use immutable versions, decimal arithmetic, explicit rounding, frozen inputs/checksums, blind evaluation where configured, moderation, and append-only recalculations.
- Merit: incomplete/ineligible inclusion, unstable ties, undisclosed corrections, and self-approval. Gate on mandatory results, store tie evidence, objection/revision runs, and enforce segregation of duties.
- Seats: race-driven over-allocation, incorrect reservation conversion, upgrade seat loss, and expired offers holding capacity. Lock seat rows, use allocation ledgers/idempotency keys, approved conversion rules, atomic upgrade swaps, expiry jobs with tenant context, and reconciliation audits.

## Milestone 1 deliverable and verification

Implemented: campaign/application enums, module provider/registration, tenant-and-institute constrained campaigns, class/programme campaign offerings, public campaign list and application shell, throttled unauthenticated draft start, hashed one-time access token, manual/assisted/import provenance columns, models, factories, seeder, and foundational public/isolation/source tests.

Commands: `php artisan migrate`, `php artisan db:seed --class=AdmissionsFoundationSeeder`, `php artisan test --filter=AdmissionsFoundationTest`, `vendor/bin/phpstan analyse`, `php artisan erp:admissions-integrity-audit`. Expected: migrations and seed succeed, all admissions tests pass, static analysis reports no new errors, tenant isolation passes, and audit emits PASS/WARNING/FAIL with non-zero status only for failures.

Deferred deliberately to their specified milestones: secure resume/OTP, dynamic form builder, documents, workflow transitions, scrutiny/assessment engines, merit, selection, offers, conversion, Filament/Livewire staff interfaces, notifications, imports, reports, and APIs.
