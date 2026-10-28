# Requirements

**Status:** Approved 2026-10-28 (Gate G0); P0 inputs pending (Gate G1) · **Date:** 2026-10-28

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**. Only items marked Confirmed are agreed requirements. Everything else is a proposal or an open question.

## 1. Confirmed requirements and context

Source: project brief supplied by the project owner on 2026-10-28, plus repository inspection.

| ID | Requirement / fact | Notes |
| --- | --- | --- |
| C-01 | The application is candidate-facing and delivered online. | Core purpose. |
| C-02 | It is a separate Laravel application. | Independent codebase from the office app. |
| C-03 | It has its own PostgreSQL database. | Implementation not configured yet; scaffold uses SQLite. |
| C-04 | HR reviews finalized assessment results in the existing internal office dashboard. | This app does not provide the HR review UI. |
| C-05 | The office application is Laravel + PostgreSQL. | Integration peer. |
| C-06 | Integration between the two applications is required; an authenticated server-to-server API is the likely approach. | API contract, direction, and environment are **Pending**. |
| C-07 | Original deadline was the end of October 2026 (2026-10-31). | Superseded 2026-10-28: sponsor initiated launch-date renegotiation (D-017); new date Pending (HRQ-28). |
| C-08 | The deliverable must be minimal and reliable. | Scope discipline required. |
| C-09 | Assessment type, questions, scoring formula, pass thresholds, retention period, and invitation workflow are not finalized. | Must not be invented. See `HR_OPEN_QUESTIONS.md`. |

Beyond these, the MVP path and boundaries (D-016), invitation-only access (D-005), and randomized presentation of the complete question set (D-020) are confirmed. In particular, no question type, scoring model, result schema, candidate identity field, or deployment target has been agreed.

## 2. Functional requirements (MVP)

These are engineering proposals for discussion. They are not commitments. The MVP path and system boundaries themselves were confirmed by the sponsor on 2026-10-28 (D-016); individual requirements below remain Proposed unless marked otherwise. Owner preferences recorded the same day — invitation creation via the office dashboard, one attempt **total** per invitation, configurable invitation expiry, and a resumable active attempt — are **Proposed** (`D-018`, `D-019`) pending HR confirmation and feasibility review.

| ID | Requirement | Status | Depends on / notes |
| --- | --- | --- | --- |
| FR-01 | A candidate enters through a unique, high-entropy invitation link. The app validates existence, status, and expiry, and shows a generic error for invalid links. | Confirmed (approach) | D-005; details Pending HRQ-09, HRQ-10, HRQ-12 |
| FR-02 | The invitation carries the candidate's identifying and reference data supplied by HR; candidates do not create accounts or self-register. | Confirmed (approach) | D-005; fields Pending HRQ-11 |
| FR-03 | Before starting, the candidate sees HR-approved instructions and consent text, and must acknowledge them. The acknowledgement timestamp and text version are stored. Acknowledgement alone is not legal consent; launch requires approved wording (HRQ-19). | Proposed | HRQ-19; text Pending |
| FR-04 | Starting creates one attempt linked to the invitation, with server-side timestamps and states such as `in_progress`, `submitted`, `expired`, `voided`. One attempt **total** per invitation: a second attempt is prohibited after submission or expiry unless an approved retake policy says otherwise. | Proposed | Owner preference (D-019); state names illustrative; HRQ-12 |
| FR-05 | Every candidate receives the complete question set from a versioned assessment definition — no subset selection. Question order and answer-option order are randomized per attempt, and the resulting order is persisted so refresh/resume does not reshuffle. | Confirmed | D-020; instrument constraints to validate (HRQ-31) |
| FR-06 | Answers are saved server-side as the candidate progresses. Refreshing or reconnecting resumes the same active attempt — no second attempt is created. | Proposed | Owner preference (D-019); must not rely on browser storage alone; feasibility to review |
| FR-07 | Invitation expiry (configurable, `D-019`) governs whether an attempt may be started. Any attempt time limit is a separate policy: if HR confirms one, remaining time is enforced server-side and the attempt auto-submits on expiry. | Proposed | HRQ-04, HRQ-12; no timer or auto-submit is assumed or invented |
| FR-08 | Submission is idempotent, locks the attempt, and shows a clear completion state. Repeat submissions cannot alter stored answers. | Proposed | — |
| FR-09 | Each response is stored with stable question and option identifiers (not display positions), the answer payload, and the assessment/question version so later scoring is reproducible. | Proposed | HRQ-05; D-020; schema depends on question types |
| FR-10 | A submitted attempt can be marked as a finalized result with a finalization timestamp and an immutable snapshot of the data HR needs. A raw snapshot is not a scored psychometric result; scoring stays out until approved. | Proposed | HRQ-17 defines "finalized"; HRQ-30 governs raw-output acceptability |
| FR-11 | Scoring is **not** implemented until HR approves a formula, thresholds, and interpretation. Until then the app stores raw responses only. When approved, scoring must use stable question/option identifiers, never randomized display positions. | Pending | HRQ-05, HRQ-06, HRQ-07; D-020 |
| FR-12 | Finalized results are handed to the office dashboard through the agreed authenticated mechanism. A documented CSV/JSON export of raw responses is the fallback **only if HR approves its fields (HRQ-08) and confirms raw responses are acceptable and sufficient for review (HRQ-30)**. | Proposed / Pending | HRQ-08, HRQ-14, HRQ-15, HRQ-30; C-06 |
| FR-13 | Intended workflow: the office dashboard requests invitation creation and receives the candidate link; revocation and status retrieval are supported (D-018). Reissue rotates the token and invalidates the old token (Proposed). Temporary pilot fallback: an Artisan command or CSV import run in this app. | Proposed | D-018; office-side capability Pending HRQ-29; HR authorization and active-attempt handling Pending; fallback is pilot-only |
| FR-14 | Key events are auditable: invitation issued/opened, consent accepted, attempt started, submitted/expired, result finalized, result exported. | Proposed | Do not log answer content or unnecessary PII. |
| FR-15 | Candidate-facing pages are responsive and usable on current mobile and desktop browsers. | Proposed | HRQ-26 if formal accessibility requirements exist |
| FR-16 | The app exposes a health endpoint and structured error handling without leaking candidate data. | Proposed | `/up` exists in the scaffold. |
| FR-17 | No public candidate registration path is exposed in production. The starter kit's public auth routes/screens are disabled or removed per D-015. | Proposed (launch blocker) | Invitation-only access Confirmed (D-005); route treatment Proposed, approval Pending (D-015) |

## 3. Non-functional requirements

| ID | Requirement | Status | Notes |
| --- | --- | --- | --- |
| NFR-01 | All candidate and integration traffic uses HTTPS. | Proposed | Enforce in production. |
| NFR-02 | Integration requests are authenticated; credentials are stored outside the repository and can be rotated. | Proposed | Mechanism Pending (HRQ-14). |
| NFR-03 | Invitation tokens are unguessable, time-limited, and stored hashed; access checks are server-side. | Proposed | Do not put sensitive data in URLs beyond the token. |
| NFR-04 | Candidate A cannot read or modify candidate B's attempt, responses, or result. | Proposed | Requires explicit tests. |
| NFR-05 | Submission and result export are transactional and safe to retry. | Proposed | Use idempotency keys for API handoff. |
| NFR-06 | Candidate identity data is minimized and logged only where necessary. | Proposed | Field list Pending (HRQ-11). |
| NFR-07 | Retention and deletion follow HR policy once confirmed; no default retention period is assumed. | Pending | HRQ-18; legal/consent HRQ-19. |
| NFR-08 | Timestamps are stored in UTC and displayed consistently. | Proposed | Confirm HR reporting timezone. |
| NFR-09 | The application has a documented backup/restore process before launch. | Pending | Depends on deployment (HRQ-22). |
| NFR-10 | Performance targets are defined before launch rather than assumed. | Pending | No invented latency/throughput numbers. |
| NFR-11 | The per-attempt randomized display order is persisted and stable across refresh/resume; scoring and result mapping use stable question/option identifiers, not positions. | Confirmed | D-020; implementation detail |

## 4. MVP acceptance criteria (Proposed)

The MVP is acceptable when:

1. An invitation link opens the correct assessment for the intended candidate; invalid, expired, revoked, and already-used links fail safely.
2. Consent is recorded before any question is shown.
3. Answers persist across refresh/reconnect while the attempt is active.
4. A submitted attempt is locked; repeated submit requests do not duplicate or change data.
5. A finalized result is retrievable by HR through the agreed handoff, or through the documented export fallback (conditional on HR confirming raw responses are acceptable, HRQ-30).
6. Cross-candidate access attempts are denied and covered by automated tests.
7. Candidate pages work on a current mobile and desktop browser.
8. Question and answer-option order is randomized per attempt, persisted across refresh/resume, and scoring identity is unaffected by display order.
9. The test suite, formatter, and static analysis pass.

## 5. Explicit non-requirements / deferred

| Item | Status | Reason |
| --- | --- | --- |
| HR dashboard, review screens, analytics | Deferred / out of scope | Provided by the office application (C-04). |
| Candidate self-registration, passwords, social login | Confirmed out (behavior) | Invitation-only access confirmed (D-005); disabling/removing the starter kit's public auth routes is Proposed (D-015) and a launch blocker. |
| Psychometric instrument design and validation | Deferred | HR responsibility. |
| Scoring and pass/fail calculation | Pending | Formula not finalized (C-09). |
| Proctoring, webcam, biometric identity checks | Deferred | Not requested; privacy and cost impact. |
| Multiple assessment instruments at launch | Deferred | Single instrument assumed for MVP (A-02). |
| Retakes and attempt resets | Deferred | Policy not defined (HRQ-12). |
| Automated email/SMS invitations | Deferred | Delivery channel Pending (HRQ-10). |
| Localization | Deferred | English assumed (A-04). |
| Native mobile apps | Deferred | Responsive web is sufficient. |
| ATS/payroll integration | Deferred | No requirement. |

## 6. Assumptions

All assumptions are **Proposed** and listed with impact in `PROJECT_BRIEF.md` §7. The requirements above depend on A-01 through A-08. If an assumption is rejected, the affected requirement must be re-scoped before implementation.

## 7. Recommendations

1. Ship raw-response capture and a result export before any scoring engine (R-01, R-02, R-03) — the export is a launch fallback only if HR approves its fields and confirms the available data is sufficient for review (HRQ-08, HRQ-30); raw responses are not automatically an acceptable result.
2. Treat assessment definitions as versioned data, not hard-coded arrays, to protect historical results.
3. Keep the integration boundary narrow: finalized result payload only, no shared database and no synchronous dependency during the candidate session.
4. Make policy values configurable, but do not invent defaults where HR has not decided.
5. Build automated tests for access control, idempotent submission, and expiry before polishing UI.

## 8. Open questions affecting requirements

| Question | Affected requirements |
| --- | --- |
| HRQ-01, HRQ-02, HRQ-03 | FR-05, FR-09; data model |
| HRQ-31 | FR-05, FR-09, FR-11; randomization constraints |
| HRQ-04 | FR-07; invitation expiry vs attempt time limit |
| HRQ-05, HRQ-06, HRQ-07 | FR-11; result payload |
| HRQ-09, HRQ-10, HRQ-11, HRQ-12 | FR-01, FR-02, FR-13 |
| HRQ-08, HRQ-14, HRQ-15, HRQ-17, HRQ-30 | FR-10, FR-12; result payload and integration |
| HRQ-29 | FR-13; invitation lifecycle |
| HRQ-18, HRQ-19 | NFR-06, NFR-07; launch approval |
| HRQ-22, HRQ-24 | NFR-09; launch |

Full text and priority: `HR_OPEN_QUESTIONS.md`.
