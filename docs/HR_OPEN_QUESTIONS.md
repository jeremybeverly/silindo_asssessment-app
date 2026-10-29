# HR Open Questions

**Status:** HR confirmed the instrument, response format, and attempt/access rules 2026-10-29; remaining questions open · **Date:** 2026-10-29

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**.

This register lists decisions owned by HR, the project sponsor, or the office application team. Engineering must not invent answers. "No default" means work on the affected slice stops until an answer is recorded here.

## Priority definitions

| Priority | Meaning |
| --- | --- |
| **P0 — Blocks implementation** | Must be answered before the affected slice is built. |
| **P1 — Blocks launch** | Coding can continue using the stated fallback, but the app cannot go live without an answer. |
| **P2 — Can wait** | No impact on the MVP deadline; decide after launch. |

**Routing (2026-10-28):** the sponsor approved sending HRQ-01, 02, 08, 09, 10, 11, and 17 to HR unchanged, with no engineering defaults. The launch date is under renegotiation (D-017); HRQ-28 asks for the new date.

**HR confirmations (2026-10-29):** HR confirmed the DISC framework, one shared bank of single-statement questions answered with exactly one **M (Most)** or **L (Least)**, an invitation access window, an assessment time limit, one attempt per invitation, and a mobile-first candidate experience. HR also stated the scoring rules are **confidential**. See D-019, D-021, D-022, D-023. These are recorded here and in the decision log; anything not stated remains **Pending**.

## Minimum decision set

**To start domain/schema work** (the scoring contract is **not** required): HRQ-02 (question content), HRQ-11 (identity fields), HRQ-33 (question lifecycle rules), and the HRQ-29 API boundary.

**To finalize candidate-flow behaviour for production:** HRQ-04 (access-window/time-limit values, expiry handling), HRQ-12 (resume/reset), HRQ-17 (finalization), HRQ-08 (result fields). The flow can be built with configuration before these land; it cannot be signed off.

**To start the scoring slice:** HRQ-32 (authorized scoring integration contract). Do not ask HR to disclose the formula itself. This gate blocks Phase 8 only.

**To launch:** the above plus HRQ-09/10 (invitation delivery), HRQ-14/15 (result API contract or accepted export), HRQ-18, HRQ-19, HRQ-22, HRQ-24, HRQ-27, HRQ-30, HRQ-31.

**To re-baseline the schedule:** HRQ-28 (new launch date; owner: project sponsor).

The interim G1 target is **2026-10-29 12:00**. With the launch date under renegotiation (HRQ-28), a later G1 shifts the schedule rather than forcing defaults; do not invent policy to keep a date.

## A. Assessment content and delivery

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-01 | ~~What assessment type/instrument will be used at launch?~~ **Answered 2026-10-29:** DISC workplace personality assessment; output describes tendencies and dominant styles; scoring rules confidential. | Determines question types, timing, result fields, and UI. | Answered | — | HR |
| HRQ-02 | What is the question bank content: how many statements, their wording and language, and the source file/format HR will supply? (Format is confirmed: one statement per question, M/L only.) | Drives schema, validation, rendering, and seeding/import tooling. | **P0** | No default. Stop. | HR |
| HRQ-03 | Are there multiple complete question sets or role-based instruments? (Subset selection is excluded: every candidate receives the complete set, confirmed D-020.) | Determines whether more than one complete set must exist; does not affect confirmed question-order randomization. | P2 | One complete set for all candidates (D-021). | HR |
| HRQ-31 | Does the instrument impose presentation constraints that conflict with randomized question order (fixed item positions, grouped/bundled items)? | Question-order randomization is Confirmed (D-020); constraints could require approved exceptions. | P1 | Apply the confirmed randomization; any exception must be explicitly approved before launch — no silent fixed order. | HR / psychometric owner |
| HRQ-04 | What is the invitation access-window duration and start/end rule, and what is the assessment time limit? On expiry: auto-submit or block? How are unanswered questions treated and reported? | Access window and time limit are confirmed to exist (D-019); the values and expiry behaviour must be enforced server-side. | **P0** (behaviour blocks the candidate flow) | No invented durations or auto-submit. Candidate flow can be built with configuration, but not launched. | HR |
| HRQ-05 | Who owns the scoring implementation, and what authorized module or specification will be provided? (Do not ask for the confidential formula in the repository.) | Scoring cannot be implemented without an authorized source. | P1 | Store raw M/L responses only; no score. | HR / psychometrics owner |
| HRQ-06 | What result fields and interpretation labels must the report display, and who approves them? | Affects result payload and the HR review experience. | P1 | Omit scores/interpretations; provide raw responses. | HR |
| HRQ-07 | Does scoring run inside this application behind the agreed interface, or in the office dashboard? | Architectural boundary; affects payload, versioning, and audit. | P1 | This app stores raw data; office app scores later. | HR + engineering |
| HRQ-08 | Exactly which detailed result fields must HR see (DISC scores, per-dimension results, dominant style, timestamps, candidate reference), and what is approved as test/mock data versus real output? | Defines the finalized-result snapshot, report layout, and export/API contract. | **P0** for result slice | No default for integration. | HR |
| HRQ-30 | If authorized scoring is not available in time, are raw response snapshots acceptable as the result HR reviews, or must launch wait for approved scoring? | Makes the export fallback (D-009) conditional; prevents presenting raw data as a scored result. | P1 | No handoff launch; raw data is not labelled a DISC result. | HR |
| HRQ-32 | What is the authorized scoring integration contract: required inputs, outputs, validation, error handling, and versioning? Which implementation/version must each finalized result record? | The only safe basis for scoring (D-022). The formula itself need not be disclosed. | P0 for scoring slice | No scoring; raw capture only. | HR / psychometrics owner |
| HRQ-33 | What are the rules for editing and archiving questions already used in attempts? Who may archive, and when is a new version created? | Protects historical meaning and the question-bank lifecycle (D-024). | P1 (P0 for question CRUD) | Version-on-edit, archive instead of delete (Proposed D-024). | HR |

## B. Invitation and candidate identity

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-09 | Who creates invitations, at what point, and in what volume? Is office-dashboard creation (owner preference, D-018) confirmed, and is a pilot CLI/CSV fallback acceptable meanwhile? | Determines the integration contract, admin tooling, and the operating procedure. | **P0** | Owner preference: office-dashboard creation (Proposed, D-018); office capability unverified (HRQ-29). No confirmed default. | HR + office app team |
| HRQ-10 | How are invitations delivered: email from this app, HR email, shared link, or another channel? | Email adds deliverability, templates, and support scope; manual links are faster. | **P0** | Owner preference: link generated via the office dashboard and sent by HR manually (Proposed, D-018); no automated email assumed. | HR |
| HRQ-11 | Which candidate identity fields are required (name, email, employee/reference ID, position, department)? What verification is needed? | Privacy, schema, result matching, and duplicate prevention. | **P0** | No default. Stop. | HR + privacy owner |
| HRQ-12 | Can a candidate resume an active attempt after closing the browser or losing connectivity? Are retakes, resets, or revocation of an active attempt ever allowed? | Affects attempt states, support workload, and whether resume is a launch requirement. | P1 | One attempt per invitation and a server-enforced time limit are **Confirmed** (D-019). Resume is **Proposed**; retakes, resets, and active-attempt revocation: no default. | HR |
| HRQ-13 | How does a candidate get help with a broken, expired, or lost link? Who can reissue it? | Defines support ownership and admin capability. | P2 | **Proposed recommendation:** reissue rotates the token — a new token is issued and the old token invalidated (raw tokens are not retained for retrieval; consistent with NFR-03). HR authorization and active-attempt handling stay Pending. | HR |

## C. Integration with the office dashboard

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-14 | Is the handoff push (this app calls the office API) or pull (office app reads from this app)? Who owns the receiving endpoint and which auth method is used? | Determines API direction, credentials, retries, and deployment. | P1 | Use authenticated CSV/JSON export fallback (D-009). | Office app team |
| HRQ-15 | What is the payload schema, candidate identifier, result identifier, and idempotency/retry rule? | Prevents duplicates and mismatched candidate records. | P1 | Export with documented columns; no live API. | Office app team + HR |
| HRQ-16 | Which environments, base URLs, IP allowlists, and test credentials exist? | Cannot test or deploy integration without them. | P1 | Staging integration Deferred. | Office app team / ops |
| HRQ-29 | Can the office application add authenticated calls for **question-bank CRUD**, invitation creation/revocation/status, and result retrieval — and on what timeline? Does similar capability already exist? | The intended HR workflows (D-018, D-023) depend on office-side development; it is not assumed to exist. | P1 (blocks the required question-bank MVP; invitation/result APIs may use pilot fallbacks) | Question-bank: sponsor-approved, time-boxed CLI/CSV seeding exception only — not a permanent substitute. Invitations/results: engineering-run seeds or export (D-018, D-009). | Office app team |
| HRQ-17 | What exactly counts as a "finalized result" that HR may review? Is submission enough, or is HR approval required? | Defines state transitions and what may be exported. | **P0** for result slice | No default. Proposed option: submitted and locked after candidate confirmation; HR approval step Pending. | HR |

## D. Data protection, retention, and consent

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-18 | How long are invitations, responses, and results retained? What triggers deletion? | Legal/privacy obligation; affects schema and purge tooling. | P1 | Retain until policy is confirmed; do not claim a retention period. **Launch blocker.** | HR / privacy owner |
| HRQ-19 | What consent text, privacy notice, legal basis, and data-controller details must be shown? | Candidates cannot be asked to consent to unspecified processing. | P1 | No launch without approved text. | HR / legal |
| HRQ-20 | Who internally may access raw responses and exports, and how is that access logged? | Privacy and least-privilege design. | P2 | Restrict to the minimum operational access; no extra UI. | HR / privacy owner |
| HRQ-21 | Are there data-residency or cross-border transfer restrictions? | Constrains deployment region and providers. | P1 | No cloud region chosen; deployment Pending. | HR / legal / ops |

## E. Operations and launch

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-22 | Where will the app be deployed, on what domain, and who operates it? | Blocks production configuration, TLS, database, secrets, and backups. | P1 | No deployment. | Project sponsor / ops |
| HRQ-23 | What monitoring, alerting, and support hours are required? | Defines operational scope and on-call expectations. | P2 | Basic uptime check and logs only. | Ops |
| HRQ-24 | Who signs off launch, and is a pilot with a small candidate group required? | Prevents an unapproved go-live. | P1 | No launch. | Project sponsor |
| HRQ-25 | Are there branding, logo, domain, or candidate communication requirements? | Affects UI polish and invitation copy. | P2 | Neutral styling; no branded email. | HR / communications |
| HRQ-26 | Are there formal accessibility or browser-support requirements? | May require specific testing or design changes. | P2 | Current mobile/desktop evergreen browsers; basic accessibility best practices. | HR |
| HRQ-27 | What backup, restore, and disaster-recovery expectations apply? | Candidate data loss is unrecoverable; needs an agreed process. | P1 | Daily database backup proposed; confirm before launch. | Ops / sponsor |
| HRQ-28 | What is the new target launch date now that 31 October is under renegotiation? | Re-baselines all roadmap phases and gates. | P0 for schedule (does not block coding start) | Keep phase order and durations; calendar dates TBD. | Project sponsor |

## Confirmed by HR (2026-10-29)

These were stated by HR and are recorded as requirements; they are no longer open questions.

| Confirmed requirement | Decision |
| --- | --- |
| DISC workplace personality assessment; output describes tendencies and dominant styles | D-021 |
| One shared question bank; one statement per question; exactly one M (Most) or L (Least) response per statement | D-021 |
| HR manages the question bank in the office dashboard via this app's authenticated API | D-023 |
| Invitation access window, assessment time limit, one attempt per invitation | D-019 |
| Mobile-first candidate experience | D-021, `REQUIREMENTS.md` C-14 |
| Scoring rules are confidential; scoring deferred until an authorized specification is supplied | D-022 |
| Questions used in attempts must keep their original meaning (versioning/locking) | D-024 |

## Still Proposed

| Proposal | Related questions | Decision |
| --- | --- | --- |
| An active attempt is resumable after refresh or browser closure, without creating a second attempt | HRQ-12 | D-019 |
| Reissue rotates the token and invalidates the old one | HRQ-13 | D-018 |
| Engineering-run CLI/CSV seeding as a pilot exception until the office APIs exist (questions: sponsor-approved and time-boxed only; never the production design) | HRQ-29 | D-018, D-023 |

## Response log

Record answers here, then update the affected requirements and decisions. Do not mark a question answered without a named source.

| ID | Answer | Answered by | Date | Reflected in |
| --- | --- | --- | --- | --- |
| HRQ-01 | DISC workplace personality assessment; intended output describes personality tendencies and dominant styles; scoring rules are confidential and not supplied. | HR | 2026-10-29 | D-021, D-022, C-10, C-15 |
| HRQ-02 (partial) | One shared question bank; one statement per question; exactly one M or L per statement (no paired statement, no A/B/C/D or Likert). Question wording, count, and source file still outstanding. | HR | 2026-10-29 | D-021, FR-05, C-11 |
| HRQ-03 | Partial: every candidate receives the complete question set; question order randomized per attempt. Remaining: multiple complete sets/role-based instruments. | Project owner | 2026-10-28 | D-020, FR-05, ROADMAP |
| D-019 rules | Invitation access window, assessment time limit, one attempt per invitation confirmed; durations and expiry behaviour outstanding (HRQ-04). Resumption still Proposed (HRQ-12). | HR | 2026-10-29 | D-019, FR-04, FR-07 |

## Rules for interpreting silence

- An unanswered question is **not** approval of a default.
- A verbal answer is recorded here with the speaker's role and date; policy content should be confirmed in writing.
- If an answer changes scope, update `REQUIREMENTS.md`, `DECISIONS.md`, and `ROADMAP.md` in the same change.
- Questions marked P0 stop the affected implementation slice; questions marked P1 stop launch, not coding.
