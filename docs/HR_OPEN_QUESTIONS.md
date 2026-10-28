# HR Open Questions

**Status:** P0 set routed to HR 2026-10-28 — responses pending · **Date:** 2026-10-28

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**.

This register lists decisions owned by HR, the project sponsor, or the office application team. Engineering must not invent answers. "No default" means work on the affected slice stops until an answer is recorded here.

## Priority definitions

| Priority | Meaning |
| --- | --- |
| **P0 — Blocks implementation** | Must be answered before the affected slice is built. |
| **P1 — Blocks launch** | Coding can continue using the stated fallback, but the app cannot go live without an answer. |
| **P2 — Can wait** | No impact on the MVP deadline; decide after launch. |

**Routing (2026-10-28):** the sponsor approved sending HRQ-01, 02, 08, 09, 10, 11, and 17 to HR unchanged, with no engineering defaults. The launch date is under renegotiation (D-017); HRQ-28 asks for the new date. Owner preferences recorded the same day (D-018, D-019) are **not** HR answers; they stay Proposed pending HR confirmation and feasibility review.

## Minimum decision set

**To start feature work:** HRQ-01, HRQ-02, HRQ-08, HRQ-09, HRQ-10, HRQ-11, HRQ-17.

**To launch:** the above plus HRQ-04, HRQ-14/15 (API contract, or written acceptance of the export fallback with agreed columns), HRQ-18, HRQ-19, HRQ-22, HRQ-24, HRQ-27, HRQ-31.

**To re-baseline the schedule:** HRQ-28 (new launch date; owner: project sponsor).

The interim G1 target is **2026-10-29 12:00**. With the launch date under renegotiation (HRQ-28), a later G1 shifts the schedule rather than forcing defaults; do not invent policy to keep a date.

## A. Assessment content and delivery

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-01 | What assessment type/instrument will be used at launch? | Determines question types, timing, scoring, result fields, and UI. | **P0** | No default. Stop. | HR |
| HRQ-02 | What is the question bank: source, format, number of questions, answer options, and required question types? | Drives schema, validation, rendering, and import tooling. | **P0** | No default. Stop. | HR |
| HRQ-03 | Are there multiple complete question sets or role-based instruments? (Subset selection is excluded: every candidate receives the complete set, confirmed D-020.) | Determines whether more than one complete set must exist; does not affect confirmed order randomization. | P2 | One complete set for all candidates; question and option order randomized per attempt (Confirmed, D-020). | HR |
| HRQ-31 | Does the instrument impose presentation constraints that conflict with randomized question or option order (fixed item positions, grouped/bundled items, meaning-bearing option order)? | Randomization is Confirmed (D-020); constraints could require approved exceptions. | P1 | Apply the confirmed randomization; any exception must be explicitly approved before launch — no silent fixed order. | HR / psychometric owner |
| HRQ-04 | Is there a time limit, attempt window, or expiry behavior? Should the app auto-submit at expiry? | Timed delivery must be enforced server-side; affects schema and candidate support. | P1 (P0 if timed delivery is mandatory) | Untimed attempt; no auto-submit. Must be confirmed before launch. | HR |
| HRQ-05 | What is the scoring formula/algorithm? Who owns and validates it? | The app cannot compute results without it; wrong scoring is worse than no scoring. | P1 | Store raw responses only; no score. | HR / psychometrics owner |
| HRQ-06 | What pass/fail thresholds or interpretation labels are required? | Affects result payload and HR decisions. | P1 | Omit thresholds; provide raw/finalized responses. | HR |
| HRQ-07 | Should scoring run in this application or in the office dashboard? | Architectural boundary; affects payload, versioning, and audit. | P1 | This app stores raw data; office app scores later. | HR + engineering |
| HRQ-08 | Exactly which result fields must HR see (scores, per-section results, timestamps, candidate reference, free-text answers)? | Defines the finalized-result snapshot and export/API contract. | **P0** for result slice | No default for integration. | HR |
| HRQ-30 | If scoring is not approved in time, are raw response snapshots acceptable as the result HR reviews, or must launch wait for approved scoring? | Makes the export fallback (D-009) conditional; prevents presenting raw data as a scored result. | P1 | No handoff launch; raw data is not labelled a result. | HR |

## B. Invitation and candidate identity

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-09 | Who creates invitations, at what point, and in what volume? Is office-dashboard creation (owner preference, D-018) confirmed, and is a pilot CLI/CSV fallback acceptable meanwhile? | Determines the integration contract, admin tooling, and the operating procedure. | **P0** | Owner preference: office-dashboard creation (Proposed, D-018); office capability unverified (HRQ-29). No confirmed default. | HR + office app team |
| HRQ-10 | How are invitations delivered: email from this app, HR email, shared link, or another channel? | Email adds deliverability, templates, and support scope; manual links are faster. | **P0** | Owner preference: link generated via the office dashboard and sent by HR manually (Proposed, D-018); no automated email assumed. | HR |
| HRQ-11 | Which candidate identity fields are required (name, email, employee/reference ID, position, department)? What verification is needed? | Privacy, schema, result matching, and duplicate prevention. | **P0** | No default. Stop. | HR + privacy owner |
| HRQ-12 | What are the invitation expiry, single-use, resume, retake, and reset rules? Is revoking an active attempt ever allowed? | Affects access control, attempt states, and support workload. | P1 | Owner preferences: one attempt **total** per invitation, configurable expiry, resumable active attempt (D-019, Proposed). Retakes, resets, and active-attempt revocation: no default. | HR |
| HRQ-13 | How does a candidate get help with a broken, expired, or lost link? Who can reissue it? | Defines support ownership and admin capability. | P2 | **Proposed recommendation:** reissue rotates the token — a new token is issued and the old token invalidated (raw tokens are not retained for retrieval; consistent with NFR-03). HR authorization and active-attempt handling stay Pending. | HR |

## C. Integration with the office dashboard

| ID | Question | Why it matters | Priority | If unanswered | Owner |
| --- | --- | --- | --- | --- | --- |
| HRQ-14 | Is the handoff push (this app calls the office API) or pull (office app reads from this app)? Who owns the receiving endpoint and which auth method is used? | Determines API direction, credentials, retries, and deployment. | P1 | Use authenticated CSV/JSON export fallback (D-009). | Office app team |
| HRQ-15 | What is the payload schema, candidate identifier, result identifier, and idempotency/retry rule? | Prevents duplicates and mismatched candidate records. | P1 | Export with documented columns; no live API. | Office app team + HR |
| HRQ-16 | Which environments, base URLs, IP allowlists, and test credentials exist? | Cannot test or deploy integration without them. | P1 | Staging integration Deferred. | Office app team / ops |
| HRQ-29 | Can the office application add authenticated calls for invitation creation, revocation, and status retrieval, and on what timeline? Does similar capability already exist? | The intended invitation workflow (D-018) depends on office-side development; it is not assumed to exist. | P1 (blocks the intended workflow; pilot fallback available) | Engineering-run Artisan/CSV pilot fallback (D-018). | Office app team |
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

## Owner preferences (2026-10-28) — not HR answers

Recorded from the project owner. Each stays **Proposed** pending HR confirmation and feasibility review; none may be treated as approved policy.

| Preference | Related questions | Decision |
| --- | --- | --- |
| HR creates each candidate invitation from the existing office dashboard | HRQ-09, HRQ-10, HRQ-29 | D-018 |
| Each invitation permits one assessment attempt | HRQ-12 | D-019 |
| Each invitation has a configurable expiry | HRQ-12 | D-019 |
| An active attempt is resumable after refresh or browser closure, without a second attempt | HRQ-12 | D-019 |

## Response log

Record answers here, then update the affected requirements and decisions. Do not mark a question answered without a named source.

| ID | Answer | Answered by | Date | Reflected in |
| --- | --- | --- | --- | --- |
| HRQ-03 | Partial: every candidate receives the complete question set; question order and answer-option order randomized per attempt. Remaining: multiple complete sets/role-based instruments. | Project owner | 2026-10-28 | D-020, FR-05, ROADMAP |

## Rules for interpreting silence

- An unanswered question is **not** approval of a default.
- A verbal answer is recorded here with the speaker's role and date; policy content should be confirmed in writing.
- If an answer changes scope, update `REQUIREMENTS.md`, `DECISIONS.md`, and `ROADMAP.md` in the same change.
- Questions marked P0 stop the affected implementation slice; questions marked P1 stop launch, not coding.
