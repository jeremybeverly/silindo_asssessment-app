# Roadmap

**Status:** Approved 2026-10-28 (Gate G0); updated 2026-10-29 with HR confirmations · **Date:** 2026-10-29 · **Target launch:** TBD — renegotiation initiated 2026-10-28 (original: 2026-10-31)

## 0. Schedule status (2026-10-29)

- Gate G0 passed: documentation approved; MVP path and system boundaries confirmed (D-016).
- Gate G2a passed: foundation complete — git, PostgreSQL, CI (D-014), 34 tests green.
- HR confirmed the instrument, response format, access window, time limit, one attempt, and mobile-first on 2026-10-29 (D-019, D-021, D-022, D-023).
- Sponsor initiated launch-date renegotiation (D-017); the new launch date is Pending (HRQ-28).
- Phases keep the sequence below; calendar dates are placeholders until the new launch date is agreed.

The scope tiers and phase sequence were approved on 2026-10-28 and extended 2026-10-29. Remaining inputs are tracked in `HR_OPEN_QUESTIONS.md`; the interim G1 target is 2026-10-29 12:00 pending re-baseline.

### Gate scopes

Gates gate different slices, not the whole project. The scoring contract blocks scoring only.

| Scope | Required inputs | Not required |
| --- | --- | --- |
| Domain/schema foundation (G1 → G2b, G3) | HRQ-02 question content, HRQ-11 identity fields, HRQ-33 question lifecycle, HRQ-29 API boundary | HRQ-32 scoring contract, HRQ-04 durations, HRQ-08 result fields |
| Candidate attempt flow (G4, G5) — buildable with configuration | HRQ-02, HRQ-11, HRQ-33 (questions seeded via approved pilot helper if the API is not ready) | HRQ-32, HRQ-29 office-side delivery |
| Candidate attempt flow — production-ready behaviour | HRQ-04 access-window/time-limit values and expiry handling, HRQ-12 resume/reset, HRQ-17 finalization, HRQ-08 result fields | — |
| Question-bank MVP integration (G6) | HRQ-29 office-side capability | HRQ-32 |
| Scoring integration (G8) | HRQ-32 authorized specification/contract | — |

**Slice exception:** the question-bank subset (questions, versions, question API) may proceed once HRQ-02 and HRQ-33 are recorded. HRQ-11 is only required for invitation/attempt/result schema, so it does not block Phase 3's question work.

## 1. Reality check

The original 31 October target is superseded; the launch date is under renegotiation (D-017). The MVP shape is:

> invitation → consent → M/L assessment → autosave → submit → finalize → result handoff (export or API) → authorized scoring when supplied

Anything not on that path is deferred. HR screens, email automation, retakes, and analytics are not MVP. Scoring is deferred behind an interface (D-022) and is **not** claimed complete until HR's authorized implementation is integrated and approved.

## 2. Scope tiers

### Must have (MVP direction Confirmed; individual items keep their own status)

The MVP path and boundaries are Confirmed (D-016). The items below keep their own statuses in `REQUIREMENTS.md`.

- Invitation-link entry with access-window, revocation, and attempt-limit checks (Confirmed, D-019).
- Candidate identity data supplied by HR.
- Instructions and consent acknowledgement, with version and timestamp.
- One shared question bank; one statement per question; exactly one M (Most) or L (Least) per statement (Confirmed, D-021).
- **Required MVP outcome:** HR manages the shared question bank from the existing office dashboard through this app's authenticated API (D-023). The office application never writes to the assessment database and holds no question business rules.
- **Intended invitation workflow:** HR requests invitation creation and manages revocation/status in the office dashboard via this app's API (D-018, confirmed workflow). Whether that integration is an absolute **production launch requirement** is **Pending** sponsor/HR confirmation (HRQ-09); office-side capability is unverified (HRQ-29).
- Every candidate receives the complete set; question order randomized per attempt and persisted across resume; M/L meanings fixed (Confirmed, D-020).
- Server-enforced time limit and one-attempt rule (Confirmed existence, D-019; durations Pending HRQ-04).
- Server-side M/L answer persistence; resumable active attempt (Proposed, D-019).
- Mobile-first candidate UI that explains M/L and enforces one selection (Confirmed).
- Submit with attempt lock; immutable finalized result snapshot.
- Authenticated CSV/JSON export of finalized results (only if HR approves the export fields and confirms raw responses are sufficient for review — HRQ-08, HRQ-30; API if the contract is ready).
- Access-control tests: no cross-candidate access.
- PostgreSQL, backups, health check, structured logs without PII.

### Should have (only if time permits)

- Result handoff API to the office dashboard (D-004).
- Results-page layout using clearly labelled mock data (D-022, FR-21).

### Could have (post-deadline)

- Authorized scoring integration and validated interpretation (D-022, HRQ-32).
- Result retry/queue and delivery receipts.
- Retakes/resets.
- Multiple assessment instruments or role-based sets.
- Branded candidate communications.

### Will not do before the deadline

- HR dashboard, question-bank UI, or result review screens in this application.
- Invented DISC scoring, mappings, thresholds, or interpretations.
- Proctoring, webcam, screen recording, or automatic cheating disqualification.
- Candidate accounts or self-registration.
- Additional languages beyond Indonesian.
- Native mobile apps.
- Psychometric validation or formula design.

## 3. Phases and gates

### Phase 0 — Documentation foundation

**Date:** 2026-10-28 · **Status:** Complete — approved 2026-10-28

- Create `AGENTS.md`, `README.md`, and the five initial docs.
- Inspect the scaffold and record confirmed baseline and gaps.
- Output: this documentation set.

**Gate G0 — Documentation review**
- Sponsor/tech lead confirm the boundary, status labels, and MVP intent.
- No feature code before G0.
- **Outcome (2026-10-28):** Approved. MVP path and boundaries confirmed (D-016); launch-date renegotiation initiated (D-017).

### Phase 1 — Remaining decision gate

**Date:** HR confirmations 2026-10-29; remaining inputs pending · **Owner:** HR + sponsor + office team

- Instrument, response format, access window, time limit, one attempt, and mobile-first are confirmed (D-019, D-021).
- Remaining inputs by scope (see Gate scopes above): schema — question content (HRQ-02), identity fields (HRQ-11), question lifecycle (HRQ-33), API boundary (HRQ-29); production candidate flow — access/time-limit values and expiry behaviour (HRQ-04), resume/reset (HRQ-12), finalization (HRQ-17), result fields (HRQ-08); scoring — authorized contract (HRQ-32).
- Record answers in `HR_OPEN_QUESTIONS.md` and decisions in `DECISIONS.md`.

**Gate G1 — Minimum decisions recorded**
- Exit criteria (schema slice): question content and identity fields written down; question lifecycle rules agreed; API boundary confirmed. This unlocks Phase 2b and Phase 3 — the scoring contract is **not** required.
- Candidate-flow behaviour is finalized only when HRQ-04, HRQ-08, HRQ-12, and HRQ-17 are recorded; until then it is buildable with configuration but not production-ready.
- Fallback if an input slips: the renegotiated launch date (HRQ-28) absorbs the delay, or scope drops via the cut lines. Do not invent policy to keep a date.

### Phase 2a — Safe foundation (no HR input required)

**Owner:** Engineering · **Status:** Complete 2026-10-28

- Initialize git; agree branch protection once a remote exists (Proposed).
- Inspect the scaffold and record gaps.
- Verify existing starter tests pass (`php artisan test --compact`).
- Development tooling: formatter/static-analysis baselines and CI checks.
- PostgreSQL setup: applied under D-014 — `.env.example`, `phpunit.xml`, and CI target PostgreSQL.

**Gate G2a — Safe foundation ready**
- **Met 2026-10-28:** git initialized, baseline committed, PostgreSQL switch applied and verified (34 tests / 83 assertions on `assessment_app_test`). No domain code.

### Phase 2b — Domain foundation (requires the schema inputs in G1 plus D-015 approval)

**Owner:** Engineering

- Model the minimum domain: question + question version, M/L response identifiers, invitation (access window, status), attempt (one per invitation, server-side deadline, persisted randomized question order), response, result.
- Add factories and model tests, including question-version immutability.
- Disable public candidate registration per D-015 once approved.
- Not blocked by the scoring contract (D-022); raw-response persistence is independent of scoring.

**Gate G2b — Domain foundation ready**
- Migrations run cleanly on PostgreSQL from scratch; tests green; no real data.

### Phase 3 — Question bank domain and authenticated API

**Owner:** Engineering

- Implement question lifecycle states (draft, active, archived) and version-on-edit.
- Expose authenticated API endpoints for list, search/filter, view, create, update, archive; validation and idempotency where needed.
- Enforce the rule that a version referenced by an attempt cannot be mutated.
- Tests: version immutability after use, M/L response identifiers, validation failures.

**Gate G3 — Question bank ready**
- The API contract is documented and tested; no operation can silently change a question used by an attempt.

### Phase 4 — Invitation and attempt lifecycle

**Owner:** Engineering

- Invitation creation, revocation, status; hashed tokens; access-window enforcement.
- One attempt per invitation; server-side attempt state and deadline; autosave; resume of the same attempt.
- Tests: second attempt refused, access window enforced, deadline enforced on direct requests.

**Gate G4 — Attempt lifecycle ready**
- A second attempt cannot be created by any route; window and deadline are enforced server-side with configured values.
- Production behaviour (auto-submit, unanswered-question handling, resume) is finalized only after HRQ-04/HRQ-12 are recorded; the mechanism is built, the policy is configured.

### Phase 5 — Mobile-first candidate assessment UI (M/L)

**Owner:** Engineering

- Invitation landing, instructions, consent acknowledgement.
- Mobile-first question screen: one statement, clear M/L explanation, exactly one selection, progress, autosave.
- Resume and submission states; clear invalid/expired/used-link screens.
- Tests: single selection enforced, mobile viewport coverage, no client-only enforcement.

**Gate G5 — Candidate flow complete**
- A candidate can complete the flow on a phone and desktop; refresh preserves answers; submitted data cannot change.
- Production sign-off of candidate-visible behaviour (timing messages, expiry, resume) waits for HRQ-04/HRQ-12; do not invent those policies.

### Phase 6 — Office application integration: questions and invitations

**Owner:** Engineering + office team

- Office dashboard calls the question-bank API for HR CRUD and preview — required MVP outcome (D-023).
- Office dashboard requests invitation creation and receives candidate links — intended workflow (D-018); capability and contract Pending (HRQ-09, HRQ-29).
- Any temporary seeding helper is documented as a pilot exception and removed once the APIs are live; it is not a substitute for the required question workflow.
- Tests: authenticated calls only; office app has no database access.

**Gate G6 — HR workflows integrated**
- **Required MVP outcome:** in staging, HR can list, create, edit, preview, and archive questions through the office dashboard (D-023).
- **Invitations:** either HR can create an invitation end-to-end through the office dashboard (D-018), or a sponsor/HR-approved, time-boxed pilot exception is recorded with a named replacement plan. A CLI/CSV or engineering-run fallback is temporary, does not satisfy the intended workflow, and is never the production design; it is **not** approved by default (HRQ-09).
- A CLI/CSV seeding exception does **not** satisfy the question-bank requirement; if used there, it is recorded, time-boxed, and replaced by the API before production.

### Phase 7 — Results page with labelled mock data

**Owner:** Engineering + HR (layout review)

- Build the finalized-result snapshot and the detailed report layout.
- Render DISC sections using **clearly labelled mock data only** — no invented interpretations.
- Keep the scoring provider behind an interface so real output can replace mock data without redesign.

**Gate G7 — Report layout approved**
- Every mock value is visibly labelled test data; nothing is presented as a real candidate result.

### Phase 8 — Authorized scoring integration and validation

**Owner:** Engineering + HR (specification owner) · **Blocked on HRQ-32**

- Implement the authorized specification/module behind the scoring interface; keep the confidential formula out of the repository where possible.
- Store the scoring implementation/version on each finalized result.
- Validate outputs against HR-supplied cases; record approval.

**Gate G8 — Scoring validated**
- HR confirms outputs for agreed test cases; scoring version recorded; no inferred mappings remain.

### Phase 9 — Integration testing, security review, and deployment preparation

**Date:** new launch date TBD (HRQ-28) · **Owner:** Engineering + ops + sponsor

- Full test suite, formatter, static analysis; staging smoke test with fake candidates (expired link, valid link, refresh/resume, deadline, submit, duplicate submit, export).
- Security review: authorization on every candidate and API route, token handling, server-side secrets.
- Confirm HTTPS, database backup/restore, logs, and access restrictions.

**Gate G9 — Launch approved**
- Launch blockers answered (HRQ-04, 08, 09/10, 14/15, 17, 18, 19, 22, 24, 27, 30, 31, 32, 33; or export accepted under HRQ-30).
- Approved privacy/consent wording is in place — the acknowledgement screen alone is not legal consent (HRQ-19).
- Retention policy approved; no unapproved retention claims (HRQ-18).
- Deployment configuration, HTTPS, access control, and backup/restore verified (HRQ-22, HRQ-27).
- Candidate-facing registration path resolved and closed before launch (D-015).
- Instrument presentation constraints validated against randomization (HRQ-31).
- Formal launch sign-off (HRQ-24).

## 4. Critical path

1. Schema inputs (HRQ-02 question content, HRQ-11 identity, HRQ-33 lifecycle, HRQ-29 boundary) → question and invitation schema.
2. Question bank + required office-dashboard API → HR question management (MVP outcome, D-023).
3. Invitation/attempt lifecycle → candidate flow (buildable with configured values; production behaviour waits on HRQ-04/HRQ-12).
4. Candidate flow → finalization and result snapshot (HRQ-17/HRQ-08 for production shape).
5. Result snapshot → labelled mock report layout.
6. Authorized scoring contract (HRQ-32) → real DISC output (independent track; does not block steps 1–5).
7. Integration testing and security review → launch.

Question content is the main schema dependency: the instrument and response format are settled (D-021), so schema design starts as soon as HRQ-02, HRQ-11, HRQ-33, and the API boundary land. The scoring contract gates only Phase 8; the report ships with labelled mock data in the meantime (D-022).

## 5. Cut lines (in order)

If the schedule slips, cut in this order:

1. **Result handoff API and invitation lifecycle API** → conditional CSV/JSON export (D-009; only with HR approval per HRQ-08/HRQ-30) plus engineering-run invitation seeding **only** as a sponsor/HR-approved, time-boxed pilot exception with a named replacement plan (HRQ-09); it is not approved by default and never becomes the production design.
2. **Question-bank integration is not a default cut.** It is a required MVP outcome (D-023). If office-side delivery cannot make the date, only an explicitly sponsor-approved, time-boxed CLI/CSV seeding exception may substitute; record it in `DECISIONS.md` and `HR_OPEN_QUESTIONS.md`, and remove it once the API ships. It does not satisfy the intended HR workflow and must never become the production design.
3. **Mock results page** → a plain finalization summary without the DISC report layout, still clearly labelled.
4. **Automated invitation email** → manual link distribution.
5. **Per-role/multiple question sets** → one complete set for all candidates (question-order randomization remains, D-020).
6. **Authorized scoring** → raw M/L responses and finalized raw result (D-022; never an invented score).
7. **UI polish** → functional, accessible-enough screens.

**Never cut:** HTTPS, access control, consent, submission integrity, immutable finalized results, question versioning, attempt/access-window enforcement, backups, audit timestamps, access-control tests, or accurate documentation. The time limit and one-attempt rule are confirmed requirements and are not cuttable; only their *configuration* is adjustable.

## 6. Post-deadline backlog (Deferred)

- Authorized scoring implementation and validated interpretation (D-022, HRQ-32).
- Push API with delivery receipts and replay.
- Automated email/SMS invitations and reminders.
- Retakes, resets, and void/reissue workflow.
- Multiple instruments and role-based complete sets (question-order randomization already applies, D-020).
- Accessibility audit and additional languages.
- Reporting/analytics in the office dashboard.
- Proctoring or identity verification, only if formally requested.

## 7. Schedule risks

| Risk | Effect | Response |
| --- | --- | --- |
| Remaining schema answers late (question content, identity, question lifecycle) | No reliable build | Enforce G1 for the schema slice; descope or renegotiate date. |
| Authorized scoring specification never supplied | Report cannot show real DISC output | Ship labelled mock layout and raw results; do not invent scores; escalate HRQ-32. |
| Question edits after use change historical meaning | Corrupted results | Enforce versioning/locking (D-024); tests for immutability. |
| Instrument requires fixed item order that conflicts with confirmed randomization | Presentation change or approval delay | Validate with HR before content import (HRQ-31); any exception needs explicit approval. |
| Office team unavailable | No live question, invitation, or result APIs | Question bank is a required MVP outcome (D-023); its seeding fallback needs sponsor approval, a time limit, and a replacement plan. Invitation integration is the confirmed intended workflow (D-018), but whether it is a production-launch requirement is **Pending** (HRQ-09): any fallback requires explicit sponsor/HR approval, a time limit, and a named replacement plan, and must not become the production design. Result export remains conditional on D-009/HRQ-08/HRQ-30. Do not schedule the APIs post-launch by default. |
| Scoring demanded at launch | Confidential formula unavailable | Escalate immediately; raw M/L fallback; do not invent. |
| Privacy/consent not approved | Cannot launch | Prepare flow with placeholder marked DRAFT; launch blocked until approved. |
| Deployment not chosen | No production environment | Decide before Phase 9; do not assume a provider. |
| Single developer, limited review | Lost work or unreviewed changes | Commit early, run `composer ci:check`, keep documentation current. |

## 8. Definition of done for the deadline

- Candidate flow works end-to-end with fake data in staging, on a phone and desktop.
- Attempt/access-window and time-limit rules are enforced server-side and tested.
- Finalized result is retrievable by HR via the agreed handoff.
- Question versions used by attempts are immutable.
- Report content is either authorized scoring output or unmistakably labelled mock data.
- Access control and submission integrity are tested.
- PostgreSQL migrations, backups, and health check are verified.
- Launch blockers are answered and signed off.
- Documentation reflects the shipped scope and statuses; scoring is not marked complete unless authorized and approved.
