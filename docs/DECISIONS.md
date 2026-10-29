# Decision Log

**Status:** Living document · **Started:** 2026-10-28

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**, **Superseded**.

This log records project and technical decisions. HR policy decisions are owned by HR and appear here only once confirmed. Nothing in this file overrides an explicit HR answer.

## How to use

- Add one entry per decision, newest last. Never rewrite history; mark an entry **Superseded** and link the replacement.
- A **Proposed** decision is not approved. Do not build on it silently.
- **Pending** means the decision is blocked on input; record the owner and trigger.
- Update `REQUIREMENTS.md`, `ROADMAP.md`, and `HR_OPEN_QUESTIONS.md` when a decision changes scope.

### Template

```markdown
## D-XXX: Title
- **Status:** Proposed | Confirmed | Pending | Deferred | Superseded by D-XXX
- **Date:** YYYY-MM-DD
- **Owner:** role
- **Context:** why a decision is needed
- **Decision:** what was decided (or the recommendation, if Proposed)
- **Impact:** what changes; what is blocked
- **Review trigger:** when to revisit
```

## Index

| ID | Decision | Status |
| --- | --- | --- |
| D-001 | Separate candidate-facing application | Confirmed |
| D-002 | Dedicated PostgreSQL database | Confirmed; local applied (D-014), production Pending |
| D-003 | No HR review UI in this application | Confirmed |
| D-004 | Authenticated server-to-server API integration | Confirmed (architecture); contract Pending; amended 2026-10-29 |
| D-005 | Invitation-link access, no candidate accounts | Confirmed (2026-10-28) |
| D-006 | Versioned assessment definitions in the database | Confirmed requirement; implementation Proposed |
| D-007 | Store raw responses; defer scoring | Confirmed deferral; implementation Pending |
| D-008 | Immutable finalized results | Proposed |
| D-009 | Export fallback for HR handoff | Proposed |
| D-010 | No HR admin UI; command/import tooling | Superseded by D-018 (2026-10-28) |
| D-011 | Deployment target | Pending |
| D-012 | Retention and deletion policy | Pending |
| D-013 | Versioned JSON API with Eloquent resources | Proposed |
| D-014 | Switch local development to PostgreSQL before feature work | Confirmed (2026-10-28); applied locally and in CI |
| D-015 | Disable public registration for candidates | Proposed (launch blocker) |
| D-016 | MVP scope and system boundaries | Confirmed (2026-10-28) |
| D-017 | Launch date renegotiation | Direction Confirmed; new date Pending |
| D-018 | Invitation lifecycle: creation requested from the office dashboard | Confirmed workflow (2026-10-29); contract Pending |
| D-019 | Invitation and attempt semantics | Confirmed (2026-10-29); remaining details Pending |
| D-020 | Randomized presentation of the complete question set | Confirmed (2026-10-28); amended 2026-10-29 |
| D-021 | DISC instrument with one-statement M/L responses | Confirmed (2026-10-29) |
| D-022 | Confidential scoring implemented only from an authorized specification | Confirmed deferral; implementation Pending |
| D-023 | Question bank managed through the office dashboard API | Confirmed boundary; contract Pending |
| D-024 | Question versioning protects historical attempts | Confirmed requirement; implementation Proposed |

## D-001: Separate candidate-facing application

- **Status:** Confirmed
- **Date:** 2026-10-28
- **Owner:** Project sponsor
- **Context:** HR needs an online candidate assessment flow; the office dashboard already serves HR.
- **Decision:** Build a separate Laravel application rather than adding candidate features to the office application.
- **Impact:** Independent deployment, database, and release cycle. No shared tables or direct database access.
- **Review trigger:** Only if the sponsor reverses the architecture.

## D-002: Dedicated PostgreSQL database

- **Status:** Confirmed target; implementation Pending
- **Date:** 2026-10-28
- **Owner:** Engineering / project sponsor
- **Context:** The application is intended to own its assessment data in PostgreSQL.
- **Decision:** Use a dedicated PostgreSQL database for this application. Do not use the office application's database.
- **Impact:** PostgreSQL is configured and verified (D-014, applied 2026-10-28): local development and tests use PostgreSQL. The production database remains Pending (HRQ-22). Migrations must stay PostgreSQL-compatible.
- **Review trigger:** Before the first domain migration.

## D-003: No HR review UI in this application

- **Status:** Confirmed
- **Date:** 2026-10-28
- **Owner:** Project sponsor
- **Context:** HR already reviews results in the internal office dashboard.
- **Decision:** This application provides candidate-facing screens and result handoff only. It does not provide HR dashboards, result review, analytics, or administration screens.
- **Impact:** Result payload and handoff reliability matter more than internal reporting UI. Avoid duplicating office functionality.
- **Review trigger:** If the office dashboard cannot receive results in time.

## D-004: Authenticated server-to-server API integration

- **Status:** Confirmed (architecture); contract and environment Pending
- **Date:** 2026-10-28
- **Owner:** Engineering / office application team
- **Context:** The two applications must exchange question-bank data, invitations, and finalized results; the exact contracts are not finalized.
- **Decision:** The applications communicate only through an authenticated HTTPS server-to-server API. The office application never connects to the assessment database directly, and the assessment application never reads the office database.
- **Impact:** Requires agreement on direction, authentication, payloads, retries/idempotency, and environments per concern. Until confirmed, treat individual integrations as unavailable and use the documented pilot fallbacks (D-009 for results; engineering-run invitation tooling for invitations).
- **Amended 2026-10-28:** scope broadened to two integration concerns — invitation lifecycle (office dashboard requests creation/revocation/status and receives the candidate link; see D-018) and finalized-result handoff. Office-side capability is not assumed (HRQ-29).
- **Amended 2026-10-29:** three integration concerns are now confirmed in principle — **question bank** (office dashboard performs CRUD against this app's authenticated API, D-023), **invitations** (D-018), and **results** (this decision). Contracts, auth mechanism, and environments remain Pending (HRQ-14, HRQ-15, HRQ-16, HRQ-29).
- **Review trigger:** Integration kickoff with the office team (HRQ-14, HRQ-15, HRQ-16, HRQ-29).

## D-005: Invitation-link access, no candidate accounts

- **Status:** Confirmed (2026-10-28)
- **Date:** 2026-10-28
- **Owner:** Project sponsor (confirmed in documentation review); HR inputs Pending
- **Context:** Candidate accounts would add registration, password recovery, and support burden; HR has not defined the invitation workflow.
- **Decision:** Candidates access one assessment through a unique, time-limited invitation link. No candidate account, password, or self-registration.
- **Impact:** Invitation issuance, expiry, revocation, and reissue tooling are required. Candidate identity comes from HR-supplied data (fields Pending, HRQ-11). Access-control tests are mandatory. Operational details — issuance, delivery, expiry, retakes — remain Pending via HRQ-09, HRQ-10, HRQ-12. Disabling or removing the starter kit's public auth routes is a separate implementation decision (D-015, Proposed) and a launch blocker.
- **Amended 2026-10-28:** one attempt **total** per invitation (never merely one active attempt), configurable invitation expiry, and a resumable active attempt added as **Proposed** semantics (D-019), pending HR confirmation and feasibility review. The access model itself is unchanged.
- **Amended 2026-10-29:** one attempt per invitation, an invitation access window, and an assessment time limit are now **Confirmed** (D-019). Resume and retake details remain Pending (HRQ-12).
- **Review trigger:** HRQ-09, HRQ-10, HRQ-11, HRQ-12 answered.

## D-006: Versioned assessment definitions in the database

- **Status:** Confirmed requirement; implementation Proposed
- **Date:** 2026-10-28
- **Owner:** Engineering
- **Context:** Questions and scoring rules may change; historical results must remain explainable.
- **Decision:** Store assessment definitions and questions as versioned records. Attempts reference the exact version used. Question content used by an attempt cannot be silently rewritten afterwards.
- **Impact:** Slightly more schema work; avoids rewriting history when content changes. Requires a versioning and locking policy for questions already used in attempts (D-024) and an authoring path through the office dashboard API (D-023).
- **Amended 2026-10-29:** question-level versioning is confirmed as a requirement; the exact edit/archive rules remain Pending (HRQ-33).
- **Review trigger:** HRQ-02 and HRQ-33 answered; before schema finalization.

## D-007: Store raw responses; defer scoring

- **Status:** Confirmed deferral; implementation Pending
- **Date:** 2026-10-28
- **Owner:** Engineering + HR
- **Context:** HR has stated that the scoring rules are confidential and the authorized formula and interpretation specification have not been provided.
- **Decision:** Persist raw responses, timing, and definition/question versions. Do not implement a scoring formula, DISC mapping, threshold, profile classification, or interpretation until HR provides an authorized specification or module (D-022).
- **Impact:** The app can collect and persist responses without inventing psychometrics. Finalized results contain raw responses until authorized scoring exists; report values and interpretations stay Empty/Pending. If HR requires scores at launch, the authorized specification must arrive before the scoring slice.
- **Amended 2026-10-28:** scoring, when approved, must key on stable question/option identifiers, never randomized display positions (D-020).
- **Amended 2026-10-29:** the deferral itself is confirmed. Scoring must never be inferred from question wording or from the M/L labels without an authorized rule (D-022).
- **Review trigger:** HRQ-05, HRQ-06, HRQ-07 answered; HRQ-32 (authorized scoring contract) received.

## D-008: Immutable finalized results

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Engineering
- **Context:** HR reviews results that must not change after submission; audits may require traceability.
- **Decision (proposal):** Once an attempt is submitted and finalized, its answers and result snapshot cannot be edited. Corrections require an explicit, audited process to be agreed with HR, not direct updates.
- **Impact:** Requires a state model, finalization timestamp, and tests preventing mutation. Administrative correction policy is Pending.
- **Review trigger:** HRQ-17 (definition of finalized) and HRQ-12 (retakes).

## D-009: Export fallback for HR handoff

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Engineering + HR
- **Context:** The API contract and office-side readiness may not be complete by the deadline.
- **Decision (proposal):** Provide a documented, authenticated CSV/JSON export of finalized results as a fallback. Do not block the candidate flow on the API.
- **Impact:** HR may need a manual import step at launch. Export format must be agreed with HR; it must exclude unnecessary PII.
- **Amended 2026-10-28:** the raw-response export fallback is **conditional** on HR approving the export fields (HRQ-08) and confirming raw responses are acceptable and sufficient for review (HRQ-30). A raw snapshot is not a scored psychometric result.
- **Review trigger:** HRQ-14 and HRQ-15 answered, or before launch if the API is not ready.

## D-010: No HR admin UI; command/import tooling

- **Status:** Superseded by D-018 (2026-10-28)
- **Date:** 2026-10-28
- **Owner:** Engineering + HR
- **Context:** HR administration belongs in the office application; building another UI is out of scope.
- **Decision (historical proposal):** Support invitation creation and result export through Artisan commands and/or a controlled CSV import. No HR-facing screens in this app.
- **Impact:** HR would need a documented operating procedure and access to run commands (or engineering support). This depends on HRQ-09.
- **Superseded (2026-10-28):** invitation creation is intended to move to the office dashboard (D-018). The CLI/CSV mechanism is demoted to a temporary pilot fallback; result export remains under D-009. The no-HR-UI-in-this-app boundary is unchanged.
- **Review trigger:** n/a — see D-018.

## D-011: Deployment target

- **Status:** Pending
- **Date:** 2026-10-28
- **Owner:** Project sponsor / operations
- **Context:** The deployment environment, domain, and operator are not finalized.
- **Decision:** Not made. Do not assume a provider, region, or hosting model (including Laravel Cloud).
- **Impact:** Blocks production configuration, TLS, database provisioning, backups, monitoring, and integration allowlisting. Local development can proceed only after D-014.
- **Review trigger:** HRQ-22 answered.

## D-012: Retention and deletion policy

- **Status:** Pending
- **Date:** 2026-10-28
- **Owner:** HR / legal or privacy owner
- **Context:** No retention period or deletion trigger has been provided.
- **Decision:** Not made. Do not invent a retention period or silently delete data.
- **Impact:** Schema should avoid blocking future deletion, but no purge job or retention claim may be shipped until confirmed. Affects privacy notice and launch approval.
- **Review trigger:** HRQ-18 and HRQ-19 answered.

## D-013: Versioned JSON API with Eloquent resources

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Engineering
- **Context:** The application exposes a machine-to-machine result handoff, and may expose candidate-facing endpoints later.
- **Decision (proposal):** Use `/api/v1` routes, Laravel API Resources, token-based authentication, and explicit idempotency for write operations.
- **Impact:** Keeps the contract versionable and consistent with Laravel conventions. Final auth mechanism still depends on HRQ-14.
- **Amended 2026-10-28:** expected to also expose invitation lifecycle endpoints (create, revoke, status) for the office dashboard (D-018); contract Pending.
- **Amended 2026-10-29:** the same API surface is expected to expose question-bank CRUD endpoints for the office dashboard (D-023). Contract and auth mechanism remain Pending (HRQ-29).
- **Review trigger:** Integration contract agreed.

## D-014: Switch local development to PostgreSQL before feature work

- **Status:** Confirmed (2026-10-28); applied locally and in CI
- **Date:** 2026-10-28
- **Owner:** Engineering
- **Context:** The scaffold originally ran on SQLite; production is intended to use PostgreSQL. Developing on a different engine risks migration and query surprises. A local PostgreSQL 18.6 instance was already running with the starter migrations applied before tracked configuration was switched.
- **Decision:** Configure a local PostgreSQL database and update `.env.example` before the first assessment migration. Production provisioning remains Pending (HRQ-22).
- **State before the switch (verified 2026-10-28, read-only checks):** recorded for history — see the applied block below for current state.
  - Local: PostgreSQL 18.6 on `127.0.0.1:5432`, database `assessment_app`, 10 tables, 5 starter migrations ran in batch 1.
  - `.env`: `DB_CONNECTION=pgsql` (local only; never committed).
  - `.env.example`: still `DB_CONNECTION=sqlite` (committed).
  - `phpunit.xml`: forces `sqlite` / `:memory:`, so the test suite never exercises PostgreSQL.
  - CI (`.github/workflows/tests.yml`): no PostgreSQL service, so CI inherits the SQLite test settings.
  - A leftover `database/database.sqlite` remains locally; it is gitignored.

- **Implementation plan (applied 2026-10-28; retained for reference):**
  1. Update `.env.example` to PostgreSQL with **placeholder** credentials (`DB_CONNECTION=pgsql`, host, port, database, user, empty password); no real secrets in the repository.
  2. Choose the test-database policy. Preferred: run tests on PostgreSQL (`assessment_app_test`) for engine parity; SQLite `:memory:` is the fallback only when no server is available. `RefreshDatabase` is already applied globally in `tests/Pest.php`.
  3. Create a separate `assessment_app_test` database locally; never reuse the development database for tests and never point at the office application's database.
  4. Add a `postgres:18` service container (with health check) to the CI workflow and align its DB env with `phpunit.xml`, so `composer ci:check` actually exercises PostgreSQL.
  5. Verification checklist: `config:clear` → `migrate:fresh` on a scratch database → `migrate:rollback` → `migrate` → `php artisan test` against PostgreSQL → `php artisan db:show` sanity check. Confirm no SQLite-only SQL and no portable-schema assumptions in migrations.
  6. Keep the stale local `database/database.sqlite` in place for now (owner decision 2026-10-28); it is gitignored and unused once the switch is verified. Removal can be decided later.
- **Impact:** Requires PostgreSQL available locally (available) and a documented setup step (above). Production database host, credentials, and provider remain Pending (HRQ-22) and are not assumed.
- **Approved and applied 2026-10-28 (project owner approved the plan):**
  - `.env.example` now targets PostgreSQL with placeholder credentials (no secrets).
  - `phpunit.xml` runs tests on `assessment_app_test` (PostgreSQL), not SQLite `:memory:`.
  - CI provisions a `postgres:18` service with a health check and matching `DB_*` env, so `composer ci:check` exercises PostgreSQL.
  - `types:check` now passes `--memory-limit=1G` (PHPStan crashed at the default 128M); the scaffold is clean at level 7.
  - **Verified locally:** `assessment_app_test` exists (owner `assessment_user`); `migrate:fresh`, `migrate:rollback`, and `migrate` all ran cleanly; the suite passed **34 tests / 83 assertions** on PostgreSQL; `composer ci:check` passed end to end (Pint, PHPStan, Pest).
  - **Test-environment guardrail:** `phpunit.xml` pins `APP_ENV`, `DB_CONNECTION`, and `DB_DATABASE` in a `<server>` block. PHPUnit's `<env force="true">` cannot override values already in `$_SERVER`, which Laravel reads first — so a shell with `.env` exported could otherwise point the suite at the development database. Verified by running the suite with `APP_ENV=local` and a nonexistent `DB_DATABASE` exported: still green, on the test database.
  - `database/database.sqlite` is intentionally kept for now (owner decision 2026-10-28); it is gitignored and unused.
- **Review trigger:** Re-verify once the test database exists; revisit before the first assessment migration and before any production database provisioning (HRQ-22).

## D-015: Disable public registration for candidates

- **Status:** Proposed (launch blocker)
- **Date:** 2026-10-28
- **Owner:** Engineering + HR
- **Context:** The starter kit exposes public registration, login, password reset, 2FA, and passkeys. Invitation-only candidate access is Confirmed (D-005), so a public candidate registration path would contradict the confirmed access model. What remains undecided is the implementation treatment of the starter kit's auth routes/screens, and whether this app needs any internal user authentication (none identified).
- **Decision (proposal):** Do not expose public registration or candidate accounts. Disable or remove the unused public auth routes/screens after approval; keep Fortify installed only if needed for internal access.
- **Impact:** Launch blocker: the candidate-facing registration path must be resolved and closed before launch. Removing routes affects the starter's dashboard/settings tests and may require test cleanup. This is an implementation decision; it does not change the confirmed invitation-only behavior in D-005.
- **Review trigger:** Explicit approval; before the launch gate (G9).

## D-016: MVP scope and system boundaries

- **Status:** Confirmed
- **Date:** 2026-10-28
- **Owner:** Project sponsor (documentation review, Gate G0)
- **Context:** The thin MVP needed an explicit go/no-go before any implementation work.
- **Decision:** Confirm the MVP path invitation → consent → assessment → autosave → submit → finalize → export, with no HR UI in this application and a separate PostgreSQL database.
- **Impact:** Gate G0 is passed; `REQUIREMENTS.md` and `ROADMAP.md` are the approved baseline. Individual requirements stay Proposed until Gate G1 confirms the HR inputs.
- **Review trigger:** Scope-change requests, or Gate G1 answers that alter the flow.

## D-017: Launch date renegotiation

- **Status:** Direction Confirmed; new date Pending
- **Date:** 2026-10-28
- **Owner:** Project sponsor
- **Context:** At approval, three calendar days remained before the original 31 October target and the seven P0 questions were unanswered.
- **Decision:** Renegotiate the launch date rather than compress scope below the confirmed MVP or invent defaults. The new date is set with HR (HRQ-28).
- **Impact:** Roadmap calendar dates are placeholders pending re-baseline. Phase order, durations, gates, and cut lines still apply. Constraint C-07 (31 October 2026) is superseded once a new date is agreed.
- **Review trigger:** HRQ-28 answered.

## D-018: Invitation lifecycle: creation requested from the office dashboard

- **Status:** Confirmed workflow (2026-10-29); contract Pending
- **Date:** 2026-10-28
- **Owner:** Project owner (preference stated 2026-10-28); HR + office application team confirmation of the contract Pending
- **Context:** HR creates each candidate invitation from the existing office dashboard. The assessment app owns invitation records, tokens, validation, attempts, responses, and finalization. The office application's ability to call new APIs is unverified.
- **Decision:** The assessment app exposes authenticated endpoints (or an equivalent agreed mechanism) for the office dashboard to request invitation creation and receive the candidate link, plus revocation and status retrieval. Reissue is modelled as revoke-and-replace: a new token is issued and the old token is invalidated; raw tokens are not retained for retrieval. Result handoff remains a separate concern (D-004/D-009). No shared database.
- **Impact:** Requires office-side development and a contract (HRQ-29, HRQ-15). Until available, the temporary pilot fallback is an engineering-run Artisan command/CSV import (formerly D-010). Supersedes D-010 for invitation creation only.
- **Amended 2026-10-29:** the workflow is confirmed — HR creates invitations in the office dashboard, which calls this application's API. Only the contract and office-side implementation timeline remain Pending.
- **Review trigger:** HRQ-09, HRQ-29, and HRQ-15; feasibility check with the office application team.

## D-019: Invitation and attempt semantics

- **Status:** Confirmed (2026-10-29); remaining details Pending
- **Date:** 2026-10-28
- **Owner:** Project owner + HR
- **Context:** Confirmed by HR/project owner on 2026-10-29: invitations have a defined access window; the assessment has a time limit; each invitation permits exactly one attempt. Earlier owner preferences (2026-10-28) covered configurable expiry and resumability without HR confirmation.
- **Decision:**
  - An invitation grants access during a **defined access window**. Outside that window the invitation cannot be used. How the window is set and displayed remains Pending (HRQ-04).
  - The assessment has a **time limit**, enforced server-side. Its duration and the exact expiry behaviour (auto-submit, unanswered questions) remain Pending (HRQ-04).
  - **One attempt per invitation.** A second attempt is prohibited after submission or expiry unless an explicitly approved retake policy says otherwise.
  - Resuming an active attempt remains **Proposed** pending HR confirmation (HRQ-12); where allowed it reopens the same attempt and never creates a second one.
  - Retakes, resets, and revocation of an active attempt remain unresolved (HRQ-12).
- **Impact:** The schema needs invitation window fields, attempt state, a server-enforced deadline, and a guarantee that a second attempt cannot be created. Timer semantics must not be invented; the deadline values are configuration, not assumptions.
- **Review trigger:** HRQ-04 (durations and expiry behaviour) and HRQ-12 (resume/retake/reset) answered.

## D-020: Randomized presentation of the complete question set

- **Status:** Confirmed (2026-10-28); amended 2026-10-29 (M/L labels)
- **Date:** 2026-10-28 (restored; requirement previously confirmed by the project owner)
- **Owner:** Project owner
- **Context:** The owner confirmed that every candidate receives the complete question set, with randomized presentation order per attempt. `HRQ-03` and the roadmap previously carried a fixed-order fallback.
- **Decision:** Every candidate receives the complete question set — no subset selection. **Question order is randomized per attempt** and the resulting order is persisted with the attempt so refresh/resume does not reshuffle. Scoring, when authorized, must use stable question/option identifiers, never display positions.
- **Amended 2026-10-29:** with the confirmed one-statement M/L format (D-021), answer-option *order* randomization does not apply: M (Most) and L (Least) are fixed meanings on a single statement, not interchangeable options. Their labels and meanings must never be swapped or randomized. Option-order randomization applies only to future item types whose options are interchangeable and meaning-neutral.
- **Impact:** Attempts need a persisted question order; rendering depends on it; scoring and result mapping must key on stable IDs. Randomization is not deferred. `HRQ-03` covers only multiple complete sets or role-based instruments; `HRQ-31` covers instrument constraints.
- **Review trigger:** `HRQ-31` constraints, if any, require explicit approval.

## D-021: DISC instrument with one-statement M/L responses

- **Status:** Confirmed (2026-10-29)
- **Date:** 2026-10-29
- **Owner:** HR / project owner
- **Context:** HR confirmed the assessment is a workplace personality assessment using the DISC framework. The intended output describes personality tendencies and dominant styles.
- **Decision:**
  - The instrument is DISC-based; the initial release uses **one shared question bank**.
  - Each question is a single statement or descriptive phrase (e.g. "Gembira/Antusias").
  - The candidate chooses **exactly one** of two responses for that statement: **M (Most)** — describes the candidate most; or **L (Least)** — describes the candidate least.
  - M and L are two alternative responses to the *same* statement. There is no paired second statement that must receive the opposite label, and no A/B/C/D or Likert variants unless HR changes the requirement.
  - The selected response is stored against stable question and response identifiers, never a display position alone.
- **Impact:** Question and response schema is shaped by this format; candidate UI must explain M/L plainly and allow one selection per question. Scoring is unaffected until the authorized specification arrives (D-022) — M and L carry no assumed DISC dimension.
- **Review trigger:** HR changes the instrument or response format; `HRQ-31` reveals constraints.

## D-022: Confidential scoring implemented only from an authorized specification

- **Status:** Confirmed deferral; implementation Pending
- **Date:** 2026-10-29
- **Owner:** HR (specification owner) + Engineering
- **Context:** HR has stated the scoring rules are confidential and the authorized formula and interpretation specification have not been provided. The report should eventually display authorized DISC results and interpretations.
- **Decision:**
  - Scoring, DISC mapping, thresholds, and interpretation are implemented **only** after HR provides an authorized specification or scoring module.
  - The application must never infer DISC scores from question wording, from the M/L labels, or from response position.
  - Scoring is isolated behind a replaceable service/interface, with a versioned contract describing inputs, outputs, validation, error handling, and versioning — without exposing the confidential formula in the repository.
  - Response collection and persistence proceed independently of scoring.
  - Each finalized result records which scoring implementation/version produced it, when compatible with the approved design.
  - Mock scoring/results are permitted for UI and integration testing only, and must be clearly labelled as test data; they are never presented as real candidate results.
  - Production scoring and result validation are not "complete" until the authorized implementation is integrated and approved.
- **Impact:** The results page can ship with clearly labelled mock data, but real DISC output waits for HRQ-32. This gates scoring only: schema, question-bank, and raw M/L capture proceed independently (`ROADMAP.md` §0 "Gate scopes"). Do not mark scoring done in any status, roadmap, or README until integrated and approved.
- **Review trigger:** HRQ-32 (authorized scoring integration contract) received; HRQ-05/06/07 answered.

## D-023: Question bank managed through the office dashboard API

- **Status:** Confirmed boundary; contract Pending
- **Date:** 2026-10-29
- **Owner:** HR + office application team + Engineering
- **Context:** HR manages questions, creates invitations, and reviews detailed finalized results in the existing office application. The assessment application owns question data and question-related business rules.
- **Decision:**
  - The office dashboard provides the HR-facing question bank UI: list, search/filter, create, view, edit, preview the candidate M/L interface, and archive/delete per lifecycle rules; draft/active states where the lifecycle requires them.
  - The office dashboard performs these operations by calling this application's **authenticated API**. It never writes to the assessment database directly.
  - This application owns the question records, validation, versioning, and lifecycle rules.
  - Question-content management stays separate from confidential scoring configuration; HR is not assumed to view or edit scoring rules.
- **Impact:** Adds a third integration concern (D-004) with its own contract, versioning, and error handling. This is a **required MVP outcome**, not an optional enhancement. A CLI/CSV seeding helper is permitted only as an explicitly approved, time-boxed pilot exception if the office-side delivery cannot make the date; it does not satisfy the intended HR workflow and must never become the production design. Office-side effort is Pending confirmation (HRQ-29).
- **Review trigger:** HRQ-29 (office capability and timeline), HRQ-33 (lifecycle rules), HRQ-02 (question content).

## D-024: Question versioning protects historical attempts

- **Status:** Confirmed requirement; implementation Proposed
- **Date:** 2026-10-29
- **Owner:** Engineering + HR
- **Context:** Editing or deleting a question after it has been used would silently change what a past candidate answered, corrupting the meaning of stored results.
- **Decision:** Questions and their response identifiers are versioned. An attempt references the exact question versions it presented. Questions used by any attempt cannot be destructively edited; changes create a new version, and archiving replaces deletion where history must be preserved. The exact edit/archive policy is Pending (HRQ-33).
- **Impact:** Schema needs question version identity (not just a mutable row), and every response keys on the version shown. Prevents retroactive meaning changes; adds modest schema and API complexity.
- **Review trigger:** HRQ-33 answered; before the question schema is finalized.

## Deliberately not decided yet

| Topic | Why it is open | Where tracked |
| --- | --- | --- |
| Question bank size and content | HR owns the instrument; DISC is confirmed but items are not supplied. | HRQ-02 |
| Instrument presentation constraints | Question order randomization is Confirmed (D-020); possible exceptions must be validated, not assumed. | HRQ-31 |
| Authorized scoring specification | Confidential; implementation waits for the contract. | HRQ-32, HRQ-05, HRQ-06, HRQ-07 |
| Result fields and definition of "finalized" | Depends on HR review needs and the authorized report format. | HRQ-08, HRQ-17 |
| Question edit/archive rules | Affects versioning and historical integrity. | HRQ-33 |
| Invitation creation and delivery details | Workflow confirmed (D-018); contract and delivery channel open. | HRQ-09, HRQ-10, HRQ-29 |
| Office dashboard capability | Office-side development unverified; not assumed to exist. | HRQ-29 |
| Access-window and time-limit behaviour | Existence confirmed (D-019); durations and expiry behaviour open. | HRQ-04 |
| Resume, retake, reset | Resume is Proposed; retake/reset undefined. | HRQ-12 |
| Raw responses as the HR-reviewed output | Depends on the scoring decision; the fallback must not be oversold as a result. | HRQ-30 |
| Candidate identity fields | Privacy and verification requirements unclear. | HRQ-11 |
| Retention and consent | Legal/privacy input required. | HRQ-18, HRQ-19 |
| Deployment and operations | Environment not chosen. | HRQ-22 |
| New launch date | Renegotiation initiated 2026-10-28 (D-017). | HRQ-28 |
