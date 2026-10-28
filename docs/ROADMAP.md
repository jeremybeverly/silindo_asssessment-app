# Roadmap

**Status:** Approved 2026-10-28 (Gate G0); schedule re-baselining · **Date:** 2026-10-28 · **Target launch:** TBD — renegotiation initiated 2026-10-28 (original: 2026-10-31)

## 0. Schedule status (2026-10-28)

- Gate G0 passed: documentation approved; MVP path and system boundaries confirmed (D-016).
- Sponsor initiated launch-date renegotiation (D-017); the new launch date is Pending (HRQ-28).
- Phases keep the sequence and durations below; calendar dates reflect the original plan and will be re-baselined once the new launch date is agreed.
- Gate G1 (P0 answers) blocks **domain-specific implementation that depends on HR inputs**; it does not block safe, non-domain foundation work.
- Safe, non-domain foundation (Phase 2a) may proceed during Phase 1; domain work still waits for Gate G1.

The scope tiers and phase sequence were approved on 2026-10-28. The plan assumes the P0 questions in `HR_OPEN_QUESTIONS.md` are answered at Gate G1; the interim target remains 2026-10-29 12:00 pending re-baseline.

## 1. Reality check

Against the original 31 October target, three calendar days remained at approval; the launch date is now under renegotiation (D-017). A full assessment platform is not deliverable. The MVP shape is unchanged — a thin, tested flow:

> invitation → consent → assessment → autosave → submit/finalize → result handoff (export or API)

Anything not on that path is deferred. Scoring, HR screens, email automation, retakes, and analytics are not MVP.

The complete product is **not** claimed deliverable by 31 October 2026: the launch date is under renegotiation (D-017), and the required HR inputs and office-side integration capability are not yet available.

## 2. Scope tiers

### Must have (MVP direction Confirmed; individual items Proposed)

The MVP path and boundaries are Confirmed (D-016). The items below keep their own statuses — most are **Proposed** in `REQUIREMENTS.md`.

- Invitation-link entry with expiry/revocation checks (expiry configurable, Proposed D-019).
- Candidate identity data supplied by HR.
- Instructions and consent acknowledgement, with version and timestamp.
- One assessment definition; every candidate receives the complete question set, with question and answer-option order randomized per attempt (Confirmed, D-020).
- Persisted per-attempt display order so refresh/resume does not reshuffle; scoring uses stable question/option identifiers, not positions (Confirmed, D-020).
- Server-side answer persistence and resume during an active attempt.
- Submit with attempt lock; immutable finalized result.
- Authenticated CSV/JSON export of finalized results (only if HR approves the export fields and confirms raw responses are sufficient for review — HRQ-08, HRQ-30; API if the contract is ready).
- Access-control tests: no cross-candidate access.
- PostgreSQL, backups, health check, structured logs without PII.

### Should have (only if time permits)

- Invitation lifecycle API for the office dashboard (D-018; office-side capability Pending HRQ-29).
- Pilot fallback tooling: Artisan/CSV invitation creation until the API exists (D-018).
- Result handoff API to the office dashboard (D-004).
- Server-enforced timer and auto-submit (only if HRQ-04 confirms a limit).
- Automated invitation email (only if HRQ-10 confirms it).

### Could have (post-deadline)

- Scoring engine and thresholds, once approved.
- Result retry/queue and delivery receipts.
- Retakes/resets.
- Multiple assessment instruments.
- Branded candidate communications.

### Will not do before the deadline

- HR dashboard or result review screens.
- Proctoring, webcam, identity verification.
- Candidate accounts or self-registration.
- Localization.
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

### Phase 1 — Decision gate

**Date:** routed to HR 2026-10-28; responses due at G1 (interim target 2026-10-29 12:00) · **Owner:** HR + sponsor + office team

- Sponsor approved routing the P0 questions unchanged, with no engineering defaults (2026-10-28).
- Answer the P0 set: HRQ-01, HRQ-02, HRQ-08, HRQ-09, HRQ-10, HRQ-11, HRQ-17.
- Confirm or reject the export fallback (D-009) if the API is not ready.
- Record answers in `HR_OPEN_QUESTIONS.md` and decisions in `DECISIONS.md`.

**Gate G1 — Minimum decisions recorded**
- Exit criteria: P0 answers written down; one assessment instrument and question format known; identity fields fixed; result fields and finalized-result definition agreed.
- Fallback if the interim target slips: the renegotiated launch date (HRQ-28) absorbs the delay, or scope drops via the cut lines. Do not invent policy to keep a date.

### Phase 2a — Safe foundation (no HR input required; may run during Phase 1)

**Owner:** Engineering

- Initialize git; agree branch protection once a remote exists (Proposed).
- Inspect the scaffold and record gaps (largely done in the documentation review; keep current).
- Verify existing starter tests pass on the unmodified scaffold (`php artisan test --compact`).
- Development tooling: formatter/static-analysis baselines and CI checks as needed.
- PostgreSQL setup **planning**: document connection, env, and migration-verification steps (execution requires D-014 approval).

**Gate G2a — Safe foundation ready**
- Clean test run on the unmodified scaffold; PostgreSQL switch plan documented; no domain code.

### Phase 2b — Domain foundation (requires Gate G1 answers plus D-014/D-015 approval)

**Owner:** Engineering

- Configure local PostgreSQL and update `.env.example` (D-014); verify starter migrations.
- Confirm CI (`composer ci:check`) runs against PostgreSQL.
- Model the minimum domain: assessment definition/version, question, invitation (with expiry/status), attempt (one total per invitation, resumable, with persisted randomized order), response, result.
- Add factories and model tests.
- Disable public candidate registration per D-015 once approved.

**Gate G2b — Domain foundation ready**
- Migrations run cleanly on PostgreSQL from scratch; tests green; no real data.

### Phase 3 — Candidate flow

**Date:** 2026-10-29 (afternoon) – 2026-10-30 (morning) · **Owner:** Engineering

- Invitation validation and candidate landing/instructions page.
- Consent acknowledgement.
- Attempt start and server-side state.
- Question rendering for the confirmed question types, using the persisted per-attempt randomized order (D-020).
- Autosave and resume of the same active attempt (D-019).
- Timer/auto-submit only if HRQ-04 confirms it.
- Submission with idempotency and lock.
- Cross-candidate access tests.

**Gate G3 — Candidate flow complete**
- A candidate can complete the flow on mobile and desktop; refresh preserves answers; submitted data cannot change.

### Phase 4 — Result finalization and handoff

**Date:** 2026-10-30 (afternoon) · **Owner:** Engineering + office team

- Finalize submitted attempt with immutable snapshot and timestamp.
- Implement authenticated export (CSV/JSON) first.
- Implement push/pull API only if HRQ-14/15 are agreed and testable.
- Add retry/idempotency and export audit record.
- Document the HR operating procedure.

**Gate G4 — Handoff ready**
- HR can retrieve a finalized result end-to-end in a staging environment, or the export has been accepted in writing — including HR approval of the export fields (HRQ-08) and confirmation that raw responses are sufficient for review (HRQ-30).

### Phase 5 — Verification and launch

**Date:** new launch date TBD (HRQ-28) · **Owner:** Engineering + ops + sponsor

- Full test suite, formatter, static analysis.
- Staging smoke test with fake candidates: expired link, valid link, refresh/resume, submit, duplicate submit, export.
- Confirm HTTPS, secrets, database backup/restore, logs, and access restrictions.
- Launch sign-off (HRQ-24) and go-live.

**Gate G5 — Launch approved**
- Launch blockers answered (P0 set plus HRQ-04, 14/15, 18, 19, 22, 24, 27, 30; or export accepted under HRQ-30).
- Approved privacy/consent wording is in place — the acknowledgement screen alone is not legal consent (HRQ-19).
- Retention policy approved; no unapproved retention claims (HRQ-18).
- Deployment configuration, HTTPS, access control, and backup/restore verified (HRQ-22, HRQ-27).
- Candidate-facing registration path resolved and closed before launch (D-015).
- Instrument presentation constraints validated against randomization (HRQ-31).
- Formal launch sign-off (HRQ-24).

## 4. Critical path

1. P0 answers → data model.
2. Data model → invitation and attempt flow.
3. Attempt flow → finalization.
4. Finalization → export/API.
5. Export/API → launch verification.

Any delay in HRQ-01/02/08/09/10/11/17 moves every later gate. Integration is deliberately off the critical path by using the fallbacks. The invitation lifecycle API (D-018) is a parallel track owned by the office application team; the pilot fallback keeps it off the critical path.

## 5. Cut lines (in order)

If the schedule slips, cut in this order:

1. **Live integrations** (result handoff API and invitation lifecycle API) → conditional CSV/JSON export (D-009; only with HR approval per HRQ-08/HRQ-30) plus engineering-run CLI/CSV invitation creation as a pilot fallback (D-018).
2. **Automated email invitations** → manual link distribution.
3. **Timer** → untimed delivery (only if HR accepts).
4. **Per-role/multiple question sets** → one complete set for all candidates (order randomization remains, D-020).
5. **Scoring** → raw responses and finalized raw result.
6. **Invitation import** → manual/seed data for the pilot.
7. **UI polish** → functional, accessible-enough screens.

**Never cut:** HTTPS, access control, consent, submission integrity, immutable finalized results, backups, audit timestamps, access-control tests, or accurate documentation.

## 6. Post-deadline backlog (Deferred)

- Approved scoring engine, thresholds, and interpretation labels.
- Push API with delivery receipts and replay.
- Automated email/SMS invitations and reminders.
- Retakes, resets, and void/reissue workflow.
- Multiple instruments and role-based complete sets (order randomization already applies, D-020).
- Accessibility audit and localization.
- Reporting/analytics in the office dashboard.
- Proctoring or identity verification, only if formally requested.

## 7. Schedule risks

| Risk | Effect | Response |
| --- | --- | --- |
| P0 answers late | No reliable build | Enforce G1; descope or renegotiate date. |
| Instrument requires fixed item/option order that conflicts with confirmed randomization | Presentation change or approval delay | Validate with HR before content import (HRQ-31); any exception needs explicit approval. |
| Office team unavailable | No live integrations (invitation API or result API) | Pilot fallbacks: CLI/CSV invitation creation and conditional raw export; schedule APIs post-launch. |
| Scoring demanded at launch | Unknown formula blocks result | Escalate immediately; raw-only fallback; do not invent. |
| Privacy/consent not approved | Cannot launch | Prepare flow with placeholder marked DRAFT; launch blocked until approved. |
| Deployment not chosen | No production environment | Decide by G4; do not assume a provider. |
| One developer, no git history | Lost work, no review | Initialize git, commit early, run CI. |

## 8. Definition of done for the deadline

- Candidate flow works end-to-end with fake data in staging.
- Finalized result is retrievable by HR via the agreed handoff.
- Access control and submission integrity are tested.
- PostgreSQL migrations, backups, and health check are verified.
- Launch blockers are answered and signed off.
- Documentation reflects the shipped scope and statuses.
