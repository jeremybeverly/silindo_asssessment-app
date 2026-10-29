# Gate G1 Readiness Report

**Date:** 2026-10-29 · **Status:** Discovery artifact — not an approved schema, plan change, or gate pass
**Sources of truth:** `docs/ROADMAP.md` (gate criteria), `docs/DECISIONS.md`, `docs/HR_OPEN_QUESTIONS.md`, `docs/REQUIREMENTS.md`, `docs/PROJECT_BRIEF.md`, plus read-only repository inspection.

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**.

## 1. Executive summary

**Gate G1 has NOT passed.** None of the three documented schema exit criteria are fully met:

| G1 exit criterion (`ROADMAP.md`) | Status |
| --- | --- |
| Question content written down | **Not met** — HRQ-02 is only partially answered (format only; wording, count, language, source file outstanding) |
| Candidate identity fields written down | **Not met** — HRQ-11 unanswered |
| Question lifecycle rules agreed | **Not met** — HRQ-33 unanswered (a Proposed default exists but is not approved) |
| API boundary confirmed | **Met for the architectural boundary** (D-004, D-023: authenticated API only, no shared database); office-side **capability** is unverified (HRQ-29) |

Consequences:
- No domain schema, model, migration, route, or candidate UI work is authorised yet.
- The scoring contract (HRQ-32) is **not** part of G1 and does not block schema or capture work (D-022).
- Safe work available now: documentation, design artifacts, and non-domain tooling. There is no domain slice that can start without at least HRQ-02 and HRQ-33.

Two environment observations need attention before implementation (details in §4): the local `.env` currently points the application at the **test** database, and there is **no API authentication package** installed.

## 2. Decision readiness for the Gate G1 inputs

| Input | Status | Evidence | Still missing | Blocked slice | Safe meanwhile |
| --- | --- | --- | --- | --- | --- |
| **HRQ-02** Question bank content | **P0 — Partially answered** | Response log 2026-10-29: one shared bank, one statement per question, exactly one M/L (D-021). Response log explicitly lists wording, count, and source file as outstanding. | Statement wording, total count, language(s), source format/file, any presentation constraints (HRQ-31) | Question schema/seeding, question-bank API payloads, candidate rendering, deterministic test fixtures | Model the *shape* (statement + M/L) in a design doc only; no migration |
| **HRQ-11** Candidate identity fields | **P0 — Unanswered** | No response-log entry; "No default. Stop." | Which fields (name, email, reference ID, position, department), verification, duplicate handling | Invitation table columns, attempt/result linkage, duplicate prevention, export identity | Nothing domain-specific; do not add identity columns |
| **HRQ-33** Question lifecycle/versioning rules | **Unanswered (P1; P0 for question CRUD)** | No response-log entry. Proposed default in D-024: version-on-edit, archive instead of delete — explicitly *not approved* | Who may edit/archive, when a new version is created, visibility of archived items, treatment of questions used in attempts | Question-bank slice acceptance criteria, lifecycle states, archive/delete endpoints | Versioning/immutability model can be drawn as Proposed design |
| **HRQ-29** API boundary and office capability | **P1 — Partially answered (boundary), capability unverified** | D-004: authenticated server-to-server API only, no direct DB access. D-023: office app manages questions via this API; assessment app owns data/rules. No evidence the office app can add or consume these calls | Office-side capability, timeline, auth mechanism, endpoint ownership, environments | Office-dashboard integration (Phase 6); final API contract/auth for Phase 3 | Build local API behind a stable interface once schema is approved; do not assume the office app can call it |

**Interpretation:** G1 is blocked by missing HR content and identity decisions (HRQ-02, HRQ-11) and lifecycle rules (HRQ-33). The API *boundary* is decided; only the office *capability* is unknown. HRQ-32 is a separate gate for Phase 8.

## 3. Other outstanding decisions and production blockers

| ID | Decision | Status | Affects |
| --- | --- | --- | --- |
| HRQ-09 | Is office-dashboard invitation creation a production-launch requirement, and may a time-boxed pilot fallback be used? | **P0 for the invitation slice; Pending** | Phase 4/6, launch scope, operating procedure |
| HRQ-28 | Revised launch date | **Pending** | All scheduling; dates remain placeholders (D-017) |
| HRQ-04 | Access-window and time-limit values; behaviour on expiry; unanswered questions | **P0 for candidate-flow behaviour; Pending** | Phase 4/5 production readiness; must not be invented |
| HRQ-08 | Detailed result fields HR must see | **P0 for result slice; Pending** | Phase 7 report shape, handoff contract, export columns |
| HRQ-17 | Definition of "finalized result" | **P0 for result slice; Pending** | Attempt state machine, lock semantics |
| HRQ-12 | Resume, retakes, resets, active-attempt revocation | **P1; Pending** (resume is Proposed) | Attempt states, support flow |
| HRQ-10 | Invitation delivery channel / email automation | **P0; Pending** (manual delivery is preferred but unconfirmed) | Invitation UX, operating procedure |
| HRQ-32 | Authorized scoring integration contract | **P0 for scoring slice; Pending** | Phase 8 only |
| D-015 | Disable starter public registration | **Proposed (launch blocker)** | Launch; `/register` is currently live |
| HRQ-18, HRQ-19, HRQ-22, HRQ-24, HRQ-27 | Retention, consent, deployment, sign-off, backup | **Pending** | Launch gate G9 |
| D-013 | `/api/v1` + API Resources + token auth | **Proposed** | First API slice (auth mechanism unresolved) |

Nothing above changes D-018's confirmed workflow or D-023's required question-bank outcome.

## 4. Verified assessment-repository findings

Read-only inspection on 2026-10-29.

| Area | Verified fact | Evidence |
| --- | --- | --- |
| PHP | 8.5.12 runtime; `composer.json` requires `^8.3` | `php -v`, `composer.json` |
| Laravel | 13.39.0 | `php artisan --version` |
| UI stack | Livewire 4.4.8, Flux 2.21.0, Tailwind 4, Vite | `composer.lock`, `package.json` |
| Auth | Fortify 1.41.0, passkeys 0.2.1; session guard | `composer.lock`, `config/auth.php` |
| Public registration | **Active**: `/register` (GET+POST), `/login`, password reset, 2FA, passkeys | `config/fortify.php` features; `php artisan route:list` |
| API surface | **None**: no `routes/api.php`, no API middleware group, no Sanctum/Passport | `routes/`, `bootstrap/app.php`, `composer.json` |
| Domain code | **None**: only `User` model; 5 starter migrations; no assessment tables/routes | `app/Models`, `database/migrations` |
| Database | PostgreSQL 18.6; 10 tables; 5 migrations ran | `php artisan db:show`, `migrate:status` |
| Test DB | `pgsql` / `assessment_app_test`, pinned via `<server>` so a sourced `.env` cannot redirect it | `phpunit.xml` |
| CI | GitHub Actions with `postgres:18` service and matching `DB_*` env | `.github/workflows/tests.yml` |
| Quality gates | Pint (Laravel preset), PHPStan/Larastan level 7 with `--memory-limit=1G`, Pest 5.3.4 | `pint.json`, `phpstan.neon`, `composer.json` |
| Baseline tests | **34 tests / 83 assertions passing** on PostgreSQL (verified this run) | `php artisan test --compact` |
| Git | Repository on `main`; `origin` remote configured (GitHub); branch ahead of the remote at inspection; branch protection unverified | `git remote -v`, `git branch -vv` |

### Environment caveats (not documentation defects)

1. **Local `.env` targets the test database.** `.env` has `DB_DATABASE=assessment_app_test`, while `.env.example`, `README.md`, and audit notes describe `assessment_app` for the app and `assessment_app_test` for tests. With `RefreshDatabase`, running the app and tests against one database risks data loss. Recommendation: point local `.env` back to `assessment_app`. **Not changed** in this task.
2. **No API authentication mechanism installed.** The first API slice needs a token/signature approach; adding a package (e.g. Sanctum) is a dependency decision requiring approval (D-013 is Proposed).

## 5. Office-application findings

**Status: unavailable for inspection.** The workspace contains only `assessment-app`; no sibling Laravel application or office dashboard repository is present (`find .. -maxdepth 2 -name artisan` returned only this app).

No assumptions are made about its Laravel version, database, auth, or API readiness — consistent with D-004/D-018/HRQ-29. The following information is required before the integration slices can be planned:

1. Repository access (or a read-only export) and its Laravel/PHP versions.
2. Authentication and role/permission model for HR users; how a server-to-server caller would be authorised.
3. Existing API routes, middleware, and any API-client pattern already used by the office app.
4. HR dashboard structure and the natural place to add question management, invitation creation, and result review.
5. Whether any invitation, assessment, or result-handoff feature already exists (to avoid duplicating it).
6. Environment configuration, secrets handling, and test conventions.
7. Network/deployment constraints (allowlists, VPN, staging URLs) and who operates them.
8. Confirmation of whether the office team can build and ship the API calls, and by when (HRQ-29).

## 6. Proposed domain-model outline (discovery only — no migrations)

Derived only from confirmed requirements (D-005, D-019, D-020, D-021, D-023, D-024). Field lists are indicative; **no schema is approved**.

| Entity | Responsibility | Key relationships | Integrity constraints |
| --- | --- | --- | --- |
| **Question** | Stable identity for one question statement across its versions. | Has many QuestionVersions. | Stable ID never reused; content lives in versions, not here. |
| **QuestionVersion** | Immutable snapshot of one statement (+ language, presentation metadata) with a version number. | Belongs to Question; referenced by attempt presentation and responses. | `(question_id, version)` unique; immutable once referenced; no destructive delete — archive only (D-024). |
| **Invitation** | Authorises one candidate to take the assessment inside an access window. | Has one Attempt (one attempt per invitation). | Unique hashed token; `window_start`/`window_end`; status; identity columns **deferred** pending HRQ-11. |
| **Attempt** | One candidate session against the question set. | Belongs to Invitation; has many AttemptQuestions and Responses; has one Result. | Unique `invitation_id` (enforces one attempt); server-set `started_at`/deadline; state machine. |
| **AttemptQuestion** | Persists the randomized question order for one attempt. | Belongs to Attempt and QuestionVersion. | Unique `(attempt_id, position)` and `(attempt_id, question_version_id)`; order chosen once. |
| **Response** | The candidate's M or L choice for one question version. | Belongs to Attempt and QuestionVersion. | Unique `(attempt_id, question_version_id)`; value constrained to `M`/`L`; stores stable IDs, never position. |
| **Result** | Immutable finalized snapshot for HR review. | Belongs to Attempt (one-to-one). | Unique `attempt_id`; `finalized_at`; snapshot payload; nullable scoring provider/version. |

**M/L representation (Proposed):** two fixed response codes (`M`, `L`) rather than per-question option rows, because the labels and meanings are confirmed and never reordered (D-020, D-021). Alternative (option rows) is possible but adds no information today.

**Deliberately unresolved:**
- Candidate identity columns (HRQ-11) — do not invent.
- Question count, wording, language, source (HRQ-02).
- Edit/archive permissions and states (HRQ-33).
- Access-window/time-limit values, expiry and auto-submit behaviour (HRQ-04).
- Resume/retake/reset states (HRQ-12).
- Finalization approval semantics (HRQ-17) and result fields (HRQ-08).
- Retention/deletion fields and purge tooling (HRQ-18).
- Scoring fields beyond a nullable provider/version marker (HRQ-32, D-022).

## 7. Recommended implementation order and dependency gates

| Order | Slice | Depends on | Can start now? |
| --- | --- | --- | --- |
| 1 | Domain foundation (Phase 2b): Question/Version, Invitation, Attempt, AttemptQuestion, Response, Result | HRQ-02, HRQ-11, HRQ-33 (+ D-015 approval for route changes) | **No** |
| 2 | Question-bank domain + authenticated API (Phase 3) | Slice 1; auth mechanism decision; HRQ-33 for lifecycle | **No** |
| 3 | Invitation and attempt lifecycle (Phase 4) | Slice 1; HRQ-04 values for production behaviour | **No** |
| 4 | Mobile-first candidate UI (Phase 5) | Slices 2–3; HRQ-04/HRQ-12 for production sign-off | **No** |
| 5 | Office-dashboard integration (Phase 6) | HRQ-29 capability, contract, auth, environments; HRQ-09 for launch status | **No** |
| 6 | Report layout with labelled mock data (Phase 7) | Slices 1–3; HRQ-08 for final shape | **No** |
| 7 | Authorized scoring (Phase 8) | HRQ-32 | **No** |
| 8 | Integration testing, security, deployment (Phase 9) | All above; HRQ-22/24/27 | **No** |

Only documentation, design, and tooling work is unblocked today.

## 8. Questions to send

**To HR**
1. HRQ-02 — provide the question bank: exact statements, count, language, source file/format.
2. HRQ-11 — required candidate identity fields and any verification.
3. HRQ-33 — who may edit/archive questions, when a new version is created, and how archived questions behave.
4. HRQ-04 — access-window and time-limit values; expiry/auto-submit behaviour; treatment of unanswered questions.
5. HRQ-08 / HRQ-17 — detailed result fields and what counts as "finalized".
6. HRQ-10 — delivery channel (manual link vs automated email).
7. HRQ-12 — resume, retakes, resets, revocation of active attempts.
8. HRQ-18 / HRQ-19 — retention and approved consent/privacy wording.
9. HRQ-32 — the authorized scoring integration contract (inputs, outputs, validation, versioning). Do **not** request the confidential formula itself.

**To the sponsor**
1. HRQ-28 — the revised launch date.
2. HRQ-09 — is office-dashboard invitation creation required for production launch; if not, approve a time-boxed fallback with a replacement plan.
3. Approve the API authentication approach and any dependency needed (D-013 is Proposed).
4. Confirm D-015 (disable public candidate registration) before launch work.

**To the office-app team**
1. HRQ-29 — can you build/consume the question-bank, invitation, and result APIs; on what timeline?
2. Preferred auth mechanism, endpoint ownership, and environments/allowlists (HRQ-14/HRQ-15/HRQ-16).
3. Where question management, invitation creation, and result review would live in the existing dashboard.
4. Confirmation that no existing invitation/assessment feature already covers this.

## 9. Next actions and first-slice acceptance criteria

**Immediate (no HR decision needed)**
1. Send the question set in §8 and record answers in `HR_OPEN_QUESTIONS.md` as they arrive.
2. Point the local `.env` back to `assessment_app` so development and tests do not share a database (owner action; not performed here).
3. Decide the API authentication mechanism (D-013 extension) and approve any dependency.
4. Keep the domain outline in §6 as a design artifact; do not create migrations.

**Recommended first domain slice once G1 passes:** **question-bank domain + authenticated API** (Phase 3). It has the smallest surface area, is required for the MVP (D-023), and unblocks the office dashboard and all content-dependent work.

Dependencies: HRQ-02 (statement shape/language), HRQ-33 (lifecycle rules), approved auth mechanism, and the D-013 API conventions.

Acceptance criteria:
- Authenticated, versioned API (`/api/v1`) with token auth; credentials server-side only.
- `list`, `retrieve`, `create`, `update`, `archive` for questions, with validation and consistent error responses.
- Updating a question referenced by an attempt creates a new version; the referenced version is immutable; archive replaces destructive delete.
- Authorisation tests: unauthenticated and unauthorised calls rejected.
- Immutability tests: a used version cannot be modified; a new version preserves stable question/response identifiers and the M/L vocabulary.
- No scoring fields, formulas, or interpretations.
- `composer ci:check` green (Pint, PHPStan level 7, Pest) on PostgreSQL.

**Definition of G1 pass (restated):** HRQ-02, HRQ-11, and HRQ-33 recorded with named owners and dates, and the API boundary confirmed. At that point Phase 2b and Phase 3 may begin; HRQ-09, HRQ-28, HRQ-04, HRQ-08, and HRQ-17 remain delivery/launch gates rather than schema gates.
