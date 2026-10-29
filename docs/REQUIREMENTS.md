# Requirements

**Status:** Approved 2026-10-28 (Gate G0); HR confirmations recorded 2026-10-29 · **Date:** 2026-10-29

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**. Only items marked Confirmed are agreed requirements. Everything else is a proposal or an open question.

## 1. Confirmed requirements and context

Source: project brief supplied by the project owner on 2026-10-28, plus repository inspection.

| ID | Requirement / fact | Notes |
| --- | --- | --- |
| C-01 | The application is candidate-facing and delivered online. | Core purpose. |
| C-02 | It is a separate Laravel application. | Independent codebase from the office app. |
| C-03 | It has its own PostgreSQL database. | Confirmed; applied 2026-10-28 (D-014). Local dev/test use PostgreSQL; production host Pending (HRQ-22). |
| C-04 | HR reviews finalized assessment results in the existing internal office dashboard. | This app does not provide the HR review UI. |
| C-05 | The office application is Laravel + PostgreSQL. | Integration peer. |
| C-06 | Integration between the two applications is required; an authenticated server-to-server API is the likely approach. | API contract, direction, and environment are **Pending**. |
| C-07 | Original deadline was the end of October 2026 (2026-10-31). | Superseded 2026-10-28: sponsor initiated launch-date renegotiation (D-017); new date Pending (HRQ-28). |
| C-08 | The deliverable must be minimal and reliable. | Scope discipline required. |
| C-09 | Question content, the authorized scoring specification, retention period, and invitation delivery channel are not finalized. | Must not be invented. See `HR_OPEN_QUESTIONS.md`. |
| C-10 | The assessment uses the **DISC** framework; the intended output describes personality tendencies and dominant styles. | Confirmed by HR 2026-10-29 (D-021). Report values and interpretations are Pending the authorized specification. |
| C-11 | One shared question bank. Each question is a **single statement or descriptive phrase**, and the candidate chooses **exactly one** response: **M (Most)** or **L (Least)** for that same statement. | Confirmed (D-021). No paired second statement, no A/B/C/D or Likert variant unless HR changes the requirement. |
| C-12 | HR manages the question bank through the existing office dashboard, which calls this application's authenticated API. | Confirmed boundary (D-023). The office app never writes to the assessment database directly. |
| C-13 | Invitations have a defined access window; the assessment has a time limit; each invitation permits one attempt. | Confirmed (D-019). Durations and expiry behaviour Pending (HRQ-04). |
| C-14 | The candidate-facing experience must be **mobile-first**. | Confirmed. Candidates are expected to take the assessment on phones. |
| C-15 | HR's scoring rules are **confidential** and have not been provided to the developer. | Confirmed (D-022). Scoring, DISC mapping, and interpretation must not be invented. |

Beyond these, the MVP path and boundaries (D-016), invitation-only access (D-005), and randomized question order (D-020) are confirmed. No candidate identity field, result schema, scoring implementation, or deployment target has been agreed.

## 2. Functional requirements (MVP)

These remain proposals where marked. The MVP path and system boundaries were confirmed by the sponsor (D-016). HR confirmed the instrument, response format, question-management model, and attempt/access rules on 2026-10-29 (`D-019`, `D-021`, `D-022`, `D-023`). Items still marked **Proposed** need technical or HR agreement; **Pending** items are blocked on HR input.

| ID | Requirement | Status | Depends on / notes |
| --- | --- | --- | --- |
| FR-01 | A candidate enters through a unique, high-entropy invitation link. The app validates existence, status, and access window, and shows a generic error for invalid links. | Confirmed (approach) | D-005, D-019; details Pending HRQ-04, HRQ-09, HRQ-10, HRQ-12 |
| FR-02 | The invitation carries the candidate's identifying and reference data supplied by HR; candidates do not create accounts or self-register. | Confirmed (approach) | D-005; fields Pending HRQ-11 |
| FR-03 | Before starting, the candidate sees HR-approved instructions and consent text, and must acknowledge them. The acknowledgement timestamp and text version are stored. Acknowledgement alone is not legal consent; launch requires approved wording (HRQ-19). | Proposed | HRQ-19; text Pending |
| FR-04 | Starting creates one attempt linked to the invitation, with server-side timestamps and states such as `in_progress`, `submitted`, `expired`, `voided`. **One attempt per invitation**: a second attempt is prohibited after submission or expiry unless an approved retake policy says otherwise. | Confirmed | D-019; state names illustrative; retake/reset Pending HRQ-12 |
| FR-05 | Every candidate receives the complete question set from a versioned assessment definition — no subset selection. Each question is one statement; the candidate picks exactly one of **M (Most)** or **L (Least)** for that statement. Question order is randomized per attempt and persisted so refresh/resume does not reshuffle. M/L labels keep fixed meanings and are never reordered or swapped. | Confirmed | D-020, D-021; instrument constraints to validate (HRQ-31) |
| FR-06 | Answers are saved server-side as the candidate progresses, so an interrupted attempt can continue without losing responses. Resuming the same attempt after refresh or browser closure is **Proposed** pending HR confirmation. | Proposed | Autosave required; resume policy Pending HRQ-12; must not rely on browser storage alone |
| FR-07 | An invitation is valid only inside its **defined access window**. The assessment has a **time limit** enforced server-side. Durations, whether an attempt auto-submits on expiry, and how unanswered questions are treated remain Pending HR input. | Confirmed (existence) | D-019; durations and expiry behaviour Pending HRQ-04 |
| FR-08 | Submission is idempotent, locks the attempt, and shows a clear completion state. Repeat submissions cannot alter stored answers. | Proposed | — |
| FR-09 | Each response stores the selected M/L value against stable question and response identifiers and the question version shown, never a display position alone, so later scoring is reproducible. | Proposed | D-021, D-024; schema depends on the final question model |
| FR-10 | A submitted attempt can be marked as a finalized result with a finalization timestamp and an immutable snapshot of the data HR needs. Detailed DISC scores and interpretations appear only after authorized scoring exists. | Proposed | HRQ-17 defines "finalized"; HRQ-08 defines result fields; D-022 |
| FR-11 | Scoring is **not** implemented until HR provides an authorized specification or module. The scoring implementation sits behind a replaceable service/interface; it must never infer DISC scores from question wording, M/L labels, or response position. Each finalized result records the scoring implementation/version used. | Confirmed (deferral) / Pending (implementation) | D-022; HRQ-32; HRQ-05–07 |
| FR-12 | Finalized results are handed to the office dashboard through the agreed authenticated mechanism. A documented CSV/JSON export of raw responses is the fallback **only if HR approves its fields (HRQ-08) and confirms raw responses are acceptable and sufficient for review (HRQ-30)**. | Proposed / Pending | HRQ-08, HRQ-14, HRQ-15, HRQ-30; C-06 |
| FR-13 | The office dashboard requests invitation creation and receives the candidate link; revocation and status retrieval are supported (D-018). Reissue rotates the token and invalidates the old one (Proposed). Temporary pilot fallback: an Artisan command or CSV import run in this app. | Confirmed (workflow) / Pending (contract) | D-018; office-side capability Pending HRQ-29; HR authorization and active-attempt handling Pending; fallback is pilot-only |
| FR-14 | Key events are auditable: invitation issued/opened, consent accepted, attempt started, submitted/expired, result finalized, result exported. | Proposed | Do not log answer content or unnecessary PII. |
| FR-15 | Candidate-facing pages are **mobile-first** and usable on current mobile and desktop browsers, with M/L explained plainly and exactly one selection allowed per question. | Confirmed (mobile-first) | HRQ-26 if formal accessibility requirements exist |
| FR-16 | The app exposes a health endpoint and structured error handling without leaking candidate data. | Proposed | `/up` exists in the scaffold. |
| FR-17 | No public candidate registration path is exposed in production. The starter kit's public auth routes/screens are disabled or removed per D-015. | Proposed (launch blocker) | Invitation-only access Confirmed (D-005); route treatment Proposed, approval Pending (D-015) |
| FR-18 | The office dashboard manages the question bank through this app's authenticated API: list, search/filter, view, create, edit, preview the candidate M/L interface, and archive/delete per lifecycle rules, with draft/active states where required. **Required MVP outcome**; a CLI/CSV seeding helper is only an approved, time-boxed pilot exception and never the production design. | Confirmed (required MVP outcome) / Pending (contract) | D-023; contract Pending HRQ-29; lifecycle rules Pending HRQ-33 |
| FR-19 | Questions are versioned. Questions used by an attempt cannot be destructively edited or deleted; changes create a new version and archiving preserves history. | Confirmed (requirement) / Proposed (implementation) | D-006, D-024; exact policy Pending HRQ-33 |
| FR-20 | API credentials are held server-side only and are never exposed to browser-side code. | Confirmed | D-004; NFR-02 |
| FR-21 | Mock scoring and mock results are used only for layout and integration testing, are clearly labelled as test data, and are never presented as real candidate results. | Confirmed | D-022 |

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
| NFR-11 | The per-attempt randomized **question** order is persisted and stable across refresh/resume; responses and any future scoring use stable question/response identifiers, never positions. M/L labels are fixed-meaning and are not reordered. | Confirmed | D-020, D-021 |
| NFR-12 | Question version identity is immutable: editing content creates a new version so historical responses keep their original meaning. | Confirmed (requirement) | D-006, D-024; policy Pending HRQ-33 |
| NFR-13 | Access windows, time limits, attempt limits, and submission validation are enforced server-side, not only in client state. | Confirmed | D-019; durations Pending HRQ-04 |

## 4. MVP acceptance criteria (Proposed)

The MVP is acceptable when:

1. An invitation link opens the correct assessment for the intended candidate inside its access window; invalid, expired, revoked, and already-used links fail safely.
2. Consent is recorded before any question is shown.
3. Answers persist across refresh/reconnect while the attempt is active.
4. A submitted attempt is locked; repeated submit requests do not duplicate or change data.
5. A finalized result is retrievable by HR through the agreed handoff, or through the documented export fallback (conditional on HR confirming raw responses are acceptable, HRQ-30).
6. Cross-candidate access attempts are denied and covered by automated tests.
7. The flow works on a phone browser first, and on desktop; M/L is explained and exactly one selection per question is enforced.
8. The time limit and attempt limit are enforced server-side, including on resume and on direct requests.
9. Question order is randomized per attempt, persisted across refresh/resume, and identity is unaffected by display order.
10. Any scoring or report shown is either authorized (D-022) or unmistakably labelled mock test data.
11. The test suite, formatter, and static analysis pass.

## 5. Explicit non-requirements / deferred

| Item | Status | Reason |
| --- | --- | --- |
| HR dashboard, review screens, analytics | Deferred / out of scope | Provided by the office application (C-04). HR does manage the **question bank** there, through this app's API (D-023). |
| Candidate self-registration, passwords, social login | Confirmed out (behavior) | Invitation-only access confirmed (D-005); disabling/removing the starter kit's public auth routes is Proposed (D-015) and a launch blocker. |
| Psychometric instrument design and validation | Deferred | HR responsibility; DISC confirmed, items not supplied. |
| Scoring formula, DISC mapping, thresholds, interpretation | Deferred pending authorized specification | Confidential HR rules not provided (D-022, HRQ-32). Must not be invented. |
| Alternative response formats (A/B/C/D, Likert, paired-statement Most/Least) | Not applicable | Confirmed one-statement M/L format (D-021). Only if HR changes the requirement. |
| Cheating monitoring, webcam, screen recording, biometrics, automatic disqualification | Deferred | Not required by HR; would add privacy burden and cost. |
| AI-generated questions, video questions, bulk question import | Deferred | Not requested; no confirmed requirement. |
| Multiple assessment instruments at launch | Deferred | Single instrument assumed for MVP (A-02). |
| Retakes and attempt resets | Deferred | Policy not defined (HRQ-12). |
| Automated email/SMS invitations | Deferred | Delivery channel Pending (HRQ-10). |
| Localization | Deferred | English assumed (A-04). |
| Native mobile apps | Deferred | Responsive web is sufficient. |
| ATS/payroll integration | Deferred | No requirement. |

## 6. Assumptions

All assumptions are **Proposed** and listed with impact in `PROJECT_BRIEF.md` §7. The requirements above depend on A-01 through A-08. If an assumption is rejected, the affected requirement must be re-scoped before implementation.

## 7. Recommendations

1. Capture and persist raw M/L responses first; do not wait on scoring. The export/handoff is a launch fallback only if HR approves its fields and confirms the available data is sufficient for review (HRQ-08, HRQ-30); raw responses are not automatically an acceptable result.
2. Isolate scoring behind a single replaceable service with a versioned contract, so the confidential formula can be integrated without touching capture or delivery (D-022).
3. Keep the question bank API and the result handoff API as separate contracts with separate auth scopes; do not let the office app write to this database (D-004, D-023).
4. Treat question content as versioned, immutable-when-used data; never edit a version that an attempt already references (D-006, D-024).
5. Keep the integration boundary narrow: no synchronous dependency on the office app during the candidate session.
6. Make policy values configurable, but do not invent defaults where HR has not decided.
7. Build automated tests for access control, attempt limits, server-side timers, and idempotent submission before polishing UI.

## 8. Open questions affecting requirements

| Question | Affected requirements |
| --- | --- |
| HRQ-02, HRQ-03 | FR-05, FR-09, FR-18; question bank and data model |
| HRQ-31 | FR-05, FR-09, FR-11; randomization constraints |
| HRQ-04 | FR-07, NFR-13; access window, time limit, expiry behaviour |
| HRQ-32 | FR-11, FR-10; authorized scoring integration |
| HRQ-05, HRQ-06, HRQ-07 | FR-11; scoring ownership and integration |
| HRQ-09, HRQ-10, HRQ-11, HRQ-12 | FR-01, FR-02, FR-13, FR-06 |
| HRQ-08, HRQ-14, HRQ-15, HRQ-17, HRQ-30 | FR-10, FR-12; result payload and integration |
| HRQ-29 | FR-13, FR-18; question and invitation APIs |
| HRQ-33 | FR-18, FR-19, NFR-12; question lifecycle |
| HRQ-18, HRQ-19 | NFR-06, NFR-07; launch approval |
| HRQ-22, HRQ-24 | NFR-09; launch |

Full text and priority: `HR_OPEN_QUESTIONS.md`.
