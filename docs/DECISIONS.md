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
| D-002 | Dedicated PostgreSQL database | Confirmed (target); implementation Pending |
| D-003 | No HR review UI in this application | Confirmed |
| D-004 | Authenticated server-to-server API integration | Proposed; amended 2026-10-28; contract Pending |
| D-005 | Invitation-link access, no candidate accounts | Confirmed (2026-10-28) |
| D-006 | Versioned assessment definitions in the database | Proposed |
| D-007 | Store raw responses; defer scoring | Proposed |
| D-008 | Immutable finalized results | Proposed |
| D-009 | Export fallback for HR handoff | Proposed |
| D-010 | No HR admin UI; command/import tooling | Superseded by D-018 (2026-10-28) |
| D-011 | Deployment target | Pending |
| D-012 | Retention and deletion policy | Pending |
| D-013 | Versioned JSON API with Eloquent resources | Proposed |
| D-014 | Switch local development to PostgreSQL before feature work | Proposed |
| D-015 | Disable public registration for candidates | Proposed (launch blocker) |
| D-016 | MVP scope and system boundaries | Confirmed (2026-10-28) |
| D-017 | Launch date renegotiation | Direction Confirmed; new date Pending |
| D-018 | Invitation lifecycle: creation requested from the office dashboard | Proposed |
| D-019 | Invitation and attempt semantics | Proposed |
| D-020 | Randomized presentation of the complete question set | Confirmed (2026-10-28) |

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
- **Impact:** The scaffold currently uses SQLite and `.env.example` points to SQLite. Configuration must be changed before feature work, with approval. Migrations must be PostgreSQL-compatible.
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

- **Status:** Proposed; contract and environment Pending
- **Date:** 2026-10-28
- **Owner:** Engineering / office application team
- **Context:** The two applications must exchange finalized results; the exact contract is not finalized.
- **Decision (proposal):** Use an authenticated HTTPS server-to-server API for finalized-result handoff. Do not connect databases directly.
- **Impact:** Requires agreement on direction (push/pull), authentication, payload, retries/idempotency, and environments. Until confirmed, treat the integration as unavailable and use D-009.
- **Amended 2026-10-28:** scope broadened to two integration concerns — invitation lifecycle (the office dashboard requests creation/revocation/status and receives the candidate link; see D-018) and finalized-result handoff. Office-side capability is not assumed (HRQ-29).
- **Review trigger:** Integration kickoff with the office team (HRQ-14, HRQ-15, HRQ-16, HRQ-29).

## D-005: Invitation-link access, no candidate accounts

- **Status:** Confirmed (2026-10-28)
- **Date:** 2026-10-28
- **Owner:** Project sponsor (confirmed in documentation review); HR inputs Pending
- **Context:** Candidate accounts would add registration, password recovery, and support burden; HR has not defined the invitation workflow.
- **Decision:** Candidates access one assessment through a unique, time-limited invitation link. No candidate account, password, or self-registration.
- **Impact:** Invitation issuance, expiry, revocation, and reissue tooling are required. Candidate identity comes from HR-supplied data (fields Pending, HRQ-11). Access-control tests are mandatory. Operational details — issuance, delivery, expiry, retakes — remain Pending via HRQ-09, HRQ-10, HRQ-12. Disabling or removing the starter kit's public auth routes is a separate implementation decision (D-015, Proposed) and a launch blocker.
- **Amended 2026-10-28:** one attempt **total** per invitation (never merely one active attempt), configurable invitation expiry, and a resumable active attempt added as **Proposed** semantics (D-019), pending HR confirmation and feasibility review. The access model itself is unchanged.
- **Review trigger:** HRQ-09, HRQ-10, HRQ-11, HRQ-12 answered.

## D-006: Versioned assessment definitions in the database

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Engineering
- **Context:** Questions and scoring rules may change; historical results must remain explainable.
- **Decision (proposal):** Store assessment definitions and questions as versioned records. Attempts reference the exact version used.
- **Impact:** Slightly more schema work; avoids rewriting history when content changes. Requires an authoring/import path (see D-010).
- **Review trigger:** HRQ-01 and HRQ-02 answered; before schema finalization.

## D-007: Store raw responses; defer scoring

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Engineering + HR
- **Context:** The scoring formula, thresholds, and interpretation are not finalized.
- **Decision (proposal):** Persist raw responses, timing, and definition/question versions. Do not implement a scoring formula or pass/fail threshold until HR approves them.
- **Impact:** The app can launch and capture data without inventing psychometrics. HR receives raw/finalized responses or a later-computed score. If HR requires a score at launch, this decision must be revisited immediately.
- **Amended 2026-10-28:** scoring, when approved, must key on stable question/option identifiers, never randomized display positions (D-020).
- **Review trigger:** HRQ-05, HRQ-06, HRQ-07 answered.

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
- **Review trigger:** Integration contract agreed.

## D-014: Switch local development to PostgreSQL before feature work

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Engineering
- **Context:** The scaffold runs on SQLite; production is intended to use PostgreSQL. Developing on a different engine risks migration and query surprises.
- **Decision (proposal):** Configure a local PostgreSQL database and update `.env.example` before the first assessment migration. Do not change configuration until documentation is approved.
- **Impact:** Requires PostgreSQL available locally and a documented setup step. Existing starter migrations should be verified on PostgreSQL.
- **Review trigger:** Explicit approval (still Proposed); execution is planned for Phase 2b after Gate G1.

## D-015: Disable public registration for candidates

- **Status:** Proposed (launch blocker)
- **Date:** 2026-10-28
- **Owner:** Engineering + HR
- **Context:** The starter kit exposes public registration, login, password reset, 2FA, and passkeys. Invitation-only candidate access is Confirmed (D-005), so a public candidate registration path would contradict the confirmed access model. What remains undecided is the implementation treatment of the starter kit's auth routes/screens, and whether this app needs any internal user authentication (none identified).
- **Decision (proposal):** Do not expose public registration or candidate accounts. Disable or remove the unused public auth routes/screens after approval; keep Fortify installed only if needed for internal access.
- **Impact:** Launch blocker: the candidate-facing registration path must be resolved and closed before launch. Removing routes affects the starter's dashboard/settings tests and may require test cleanup. This is an implementation decision; it does not change the confirmed invitation-only behavior in D-005.
- **Review trigger:** Explicit approval; before Gate G5.

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

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Project owner (preference stated 2026-10-28); HR + office application team confirmation Pending
- **Context:** The project owner prefers that HR creates each candidate invitation from the existing office dashboard. The assessment app owns invitation records, tokens, validation, attempts, responses, and finalization. The office application's ability to call new APIs is unverified.
- **Decision (proposal):** The assessment app exposes authenticated endpoints (or an equivalent agreed mechanism) for the office dashboard to request invitation creation and receive the candidate link, plus revocation and status retrieval. Reissue is modelled as revoke-and-replace: a new token is issued and the old token is invalidated; raw tokens are not retained for retrieval. Result handoff remains a separate concern (D-004/D-009). No shared database.
- **Impact:** Requires office-side development and a contract (HRQ-29, HRQ-15). Until available, the temporary pilot fallback is an engineering-run Artisan command/CSV import (formerly D-010). Supersedes D-010 for invitation creation only.
- **Review trigger:** HRQ-09, HRQ-29, and HRQ-15; feasibility check with the office application team.

## D-019: Invitation and attempt semantics

- **Status:** Proposed
- **Date:** 2026-10-28
- **Owner:** Project owner (preference stated 2026-10-28); HR confirmation Pending
- **Context:** Owner preferences (2026-10-28): configurable invitation expiry; one attempt **total** per invitation; an active attempt resumable after refresh or browser closure without creating a second attempt. These are preferences, not approved policy.
- **Decision (proposal):** Invitation expiry (configurable) governs whether an attempt may be started. A separate attempt time limit or deadline is **not** assumed; no timer or auto-submit until HR defines one (HRQ-04). One attempt **total** per invitation: a second attempt is prohibited after submission or expiry unless an explicitly approved retake policy says otherwise; resume reopens the same attempt. Retakes, resets, and revocation of an active attempt remain unresolved (HRQ-12).
- **Impact:** Data model needs invitation status/expiry plus attempt state and resumability, preventing second attempts after submission or expiry — not just concurrent duplicates. Resumability needs a feasibility review against the chosen session/autosave approach.
- **Review trigger:** HRQ-04 and HRQ-12 answered; feasibility check at design time.

## D-020: Randomized presentation of the complete question set

- **Status:** Confirmed
- **Date:** 2026-10-28 (restored; requirement previously confirmed by the project owner)
- **Owner:** Project owner
- **Context:** The owner confirmed that every candidate receives the complete question set, while question order and answer-option order are randomized per attempt. This requirement was missing from the current documentation; `HRQ-03` and the roadmap incorrectly carried a fixed-order fallback. Restored in this pass.
- **Decision:** Every candidate receives the complete question set — no subset selection. Question order and answer-option order are randomized per attempt and the resulting order is persisted with the attempt so refresh/resume does not reshuffle. Scoring, when approved, must use stable question/option identifiers, never display positions. Instrument constraints that may conflict are a validation point (`HRQ-31`), not grounds to reverse randomization silently.
- **Impact:** Attempts need a persisted presentation order; rendering depends on it; scoring and result mapping must key on stable IDs. Randomization is not deferred to post-deadline. `HRQ-03` now covers only multiple complete sets or role-based instruments.
- **Review trigger:** `HRQ-31` constraints, if any, require explicit approval.

## Deliberately not decided yet

| Topic | Why it is open | Where tracked |
| --- | --- | --- |
| Assessment type and question content | HR owns the instrument. | HRQ-01, HRQ-02 |
| Instrument presentation constraints | Randomization is Confirmed (D-020); possible instrument exceptions must be validated, not assumed. | HRQ-31 |
| Scoring formula and thresholds | HR/psychometrics decision; must not be invented. | HRQ-05, HRQ-06, HRQ-07 |
| Result fields and definition of "finalized" | Depends on HR review needs. | HRQ-08, HRQ-17 |
| Invitation creation and delivery details | Owner prefers office-dashboard creation (D-018); workflow and office capability undefined. | HRQ-09, HRQ-10, HRQ-29 |
| Office dashboard invitation capability | Office-side development unverified; not assumed to exist. | HRQ-29 |
| Raw responses as the HR-reviewed output | Depends on the scoring decision; the fallback must not be oversold as a result. | HRQ-30 |
| Candidate identity fields | Privacy and verification requirements unclear. | HRQ-11 |
| Retention and consent | Legal/privacy input required. | HRQ-18, HRQ-19 |
| Deployment and operations | Environment not chosen. | HRQ-22 |
| New launch date | Renegotiation initiated 2026-10-28 (D-017). | HRQ-28 |
