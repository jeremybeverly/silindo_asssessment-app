# Project Brief

**Status:** Approved 2026-10-28 (Gate G0) · **Date:** 2026-10-28 · **Launch date:** under renegotiation (original target 2026-10-31)

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**.

## 1. Problem

**Status:** Confirmed (project brief).

HRD needs candidates to complete a workplace personality assessment — using the **DISC** framework — online, and needs HR to review the finalized result in the existing internal office dashboard. Today there is no candidate-facing assessment application and no agreed data handoff between such an application and the office system.

## 2. Purpose

**Status:** Confirmed (project brief).

Deliver a minimal, reliable candidate assessment application — launch date under renegotiation (D-017) — that:

1. Lets invited candidates complete a DISC-based workplace personality assessment on a phone-first interface.
2. Captures each answer as a single M (Most) or L (Least) choice per statement, plus timing and version data.
3. Produces a finalized result snapshot that HR can review in the office dashboard, with authorized DISC results and interpretations added once HR supplies the confidential scoring specification (D-022).

## 3. Users and systems

| Actor / system | Role | Status |
| --- | --- | --- |
| Candidate | External person who receives an invitation and completes the assessment. | Confirmed |
| HR reviewer | Reviews finalized results in the existing office dashboard, not in this app. | Confirmed |
| HR question manager | Manages the question bank (list, search, create, edit, preview, archive) in the office dashboard, which calls this app's API. | Confirmed (D-023) |
| HR / assessment owner | Owns assessment content, the confidential scoring specification, thresholds, retention, and invitation policy. | Confirmed role; content and policy Pending |
| Assessment App | This Laravel application, with its own PostgreSQL database. | Confirmed |
| Office dashboard | Existing Laravel + PostgreSQL internal application used by HR. | Confirmed |
| Integration | Three concerns: question bank (office → app), invitation lifecycle (office → app), finalized-result handoff (app → office). | Confirmed architecture (D-004); contracts Pending |

**Confirmed:** invitations are prepared by HR in the office dashboard (D-018); no HR administration UI is built in this application.

## 4. Scope

### In scope

- Candidate-facing DISC assessment flow: mobile-first, one statement per question, exactly one M/L selection. **Confirmed**
- Storage of assessment definitions, questions, invitations, attempts, responses, and results in this application's database. **Confirmed**
- Ownership of question data and question rules, invitation records, tokens, validation, attempts, responses, and result finalization in this app. **Confirmed** boundary
- Question-bank integration: HR manages questions in the office dashboard via this app's authenticated API. **Confirmed, required MVP outcome** (contract Pending).
- Invitation lifecycle integration with the office dashboard: creation on request, revocation, status retrieval. **Confirmed** workflow (contract Pending)
- Finalization of results with an immutable snapshot. **Proposed** (definition Pending)
- Handoff of finalized results to the office dashboard. **Confirmed need**; mechanism Pending
- Authorized DISC scoring and interpretation, integrated only from HR's confidential specification. **Deferred** (D-022)
- Basic operational safeguards: authentication of integration traffic, audit timestamps, error handling, backups. **Proposed**

### Out of scope (Confirmed or Proposed)

| Item | Status | Note |
| --- | --- | --- |
| HR dashboard, question-bank UI, result review UI, analytics | **Confirmed out** | Exists in the office application; question management happens there against this app's API (D-023). |
| Psychometric instrument design, DISC scoring science, interpretation rules | **Confirmed out** | HR responsibility; the confidential specification is not provided (D-022). |
| Candidate accounts, self-registration, social login | **Confirmed out** | Invitation-only access confirmed (D-005, 2026-10-28); disabling/removing the starter kit's public auth routes is Proposed (D-015) and a launch blocker. |
| Proctoring, webcam, identity verification | **Deferred** | Not requested; would add cost and privacy burden. |
| ATS/payroll integration, multi-tenant support | **Deferred** | No requirement. |
| Localization beyond English | **Deferred** | Confirm with HR if candidates require other languages. |
| Native mobile apps | **Deferred** | Responsive web only. |

## 5. System context

```text
Candidate browser (mobile-first)
      ^
      | HTTPS, invitation link (link created via office dashboard, D-018)
      |
Assessment App (Laravel, PostgreSQL)
  owns questions + versions, invitations, tokens, attempts, responses,
  result finalization; scoring behind an authorized service (D-022)
      |   ^                          ^
      |   | question CRUD API        | invitation create / revoke / status
      |   | (D-023)                  | (D-018)
      |   |                          |
      |   +--------------------------+
      |                |
      | finalized results (D-004)
      v                |
Office Dashboard (existing Laravel, PostgreSQL) — the HR interface
  question bank UI · invitation creation · result review
```

- The two applications have **separate databases** (Confirmed). The office app never writes to this database.
- The office dashboard is the only HR interface (Confirmed).
- The assessment app owns questions and question rules, invitation records, token validation, attempts, responses, scoring, and finalization (Confirmed boundary).
- Integration has three concerns: question bank (office → app, D-023), invitation lifecycle (office → app, D-018), and result handoff (app → office, D-004). Contracts are Pending.
- The office dashboard remains the system of record for HR review, not for assessment responses.

## 6. Constraints

| Constraint | Status |
| --- | --- |
| Deadline: original 31 Oct 2026. | Original target; renegotiation initiated 2026-10-28 (D-017), new date Pending |
| Minimal, reliable deliverable. | Confirmed / derived |
| Assessment app is a separate Laravel application with its own PostgreSQL database. | Confirmed |
| Existing office app is Laravel + PostgreSQL. | Confirmed |
| Instrument is DISC, one statement per question, one M/L selection; candidate flow is mobile-first. | Confirmed (D-021) |
| HR's scoring rules are confidential and the authorized specification has not been provided. | Confirmed (D-022) |
| Question content, result fields, retention, and delivery channel are not finalized. | Confirmed |
| Deployment environment and integration contracts are not finalized. | Confirmed |
| No real candidate data or credentials may be used during development. | Confirmed project rule |

## 7. Assumptions (Proposed unless marked otherwise)

| ID | Assumption | Impact if wrong |
| --- | --- | --- |
| A-01 | Candidates access the assessment through a unique invitation link, without creating an account. **Confirmed via D-005 (2026-10-28).** | Would require authentication, registration, and password-reset scope. |
| A-02 | One assessment instrument (DISC) is launched; multiple instruments or role-based sets come later. | Schema and UI must support multiple definitions sooner. |
| A-03 | One attempt per invitation; an invitation has an access window and the assessment has a time limit. **Confirmed (D-019); durations Pending.** Resuming an active attempt is **Proposed** (HRQ-12). | Durations, resume, and retake rules change the data model and support process. |
| A-04 | The assessment is delivered in English. | Adds translation and content-versioning scope. |
| A-05 | Raw M/L responses can be stored and reviewed before the scoring specification is available. | If raw storage is not acceptable, scoring must be decided before schema work. |
| A-06 | A CSV/JSON export is an acceptable temporary handoff if the API is not ready, **provided HR approves the export fields and confirms the available data suffices for review (HRQ-30)**. | Manual HR work or a hard dependency on the office team. |
| A-07 | The office team can expose or consume an authenticated endpoint. | Integration may need a different mechanism or manual import. |
| A-08 | Candidates use modern desktop or mobile browsers with a stable connection. | Offline/low-bandwidth support becomes necessary. |

## 8. Recommendations

| ID | Recommendation | Status |
| --- | --- | --- |
| R-01 | Freeze a thin MVP: invitation → consent → assessment → autosave → submit → finalize → export/API. | Proposed |
| R-02 | Store raw M/L responses with question/version identifiers; add scoring only from HR's authorized specification, behind a replaceable service, and record the scoring version per result. | Proposed |
| R-03 | Build an authenticated CSV/JSON export first and the API second, so HR is never blocked by the integration — viable only if HR approves the export fields and confirms the available data is sufficient for review (HRQ-30). | Proposed |
| R-04 | Do not build an HR UI in this application. | Proposed |
| R-05 | Keep policy values (time limit, attempt rules, retention) configurable, not hard-coded. | Proposed |
| R-06 | Collect the minimum candidate identity fields needed by HR; confirm them before schema work. | Proposed |
| R-07 | Treat this repository as the only source for assessment data; never read the office database directly. | Proposed |
| R-08 | Build the candidate capture flow before the report; a clearly labelled mock results page de-risks layout without inventing DISC output. | Proposed |
| R-09 | Version questions on write and lock versions referenced by attempts, so editing content never rewrites history. | Proposed |

## 9. Success criteria

Proposed definition of success:

1. An invited candidate can open the link on a phone, understand the M/L instruction and consent, complete the assessment, and submit without engineering support.
2. Answers survive a page refresh or brief connection loss during an active attempt.
3. The access window, time limit, and one-attempt rule are enforced server-side.
4. Every candidate receives the complete applicable question set; question order is randomized per attempt, persisted, and stable across refresh/resume; responses reference stable question/response identifiers rather than display positions; M/L labels keep fixed meanings and are never swapped or randomized (D-020).
5. A submitted attempt is locked and cannot be altered by the candidate.
6. HR can retrieve a finalized result for the candidate through the agreed handoff (API or documented export).
7. No candidate can access another candidate's attempt or result.
8. HR can manage questions from the office dashboard without touching the assessment database — required MVP outcome, not a CLI substitute.
9. Report content is either produced by the authorized scoring implementation or clearly labelled mock data — never an invented DISC interpretation.
10. The flow works on a modern mobile browser and a desktop browser.
11. All automated tests pass; no secrets or real candidate data are present in the repository.

## 10. Risks

| Risk | Likelihood | Impact | Mitigation |
| --- | --- | --- | --- |
| HR decisions (question content, authorized scoring, invitation delivery) arrive late. | High | High | Ship capture + labelled mock report; keep scoring behind an interface; enforce decision gates. |
| Authorized scoring specification is never supplied, or arrives without a usable contract. | Medium | High | Capture and report raw M/L data; never infer DISC output; escalate via HRQ-32 before the scoring slice. |
| Question edits after use silently change historical meaning. | Medium | High | Version questions; lock versions referenced by attempts; archive instead of delete (D-024, HRQ-33). |
| Original 31 October 2026 target is superseded; launch date under renegotiation. | High | High | Re-baseline the schedule once the new date is agreed (HRQ-28); enforce cut lines; keep the MVP thin. |
| Office-side development needed for both integrations (invitation APIs and result handoff); capability unverified. | Medium | High | Pilot fallbacks: CLI/CSV invitation creation and conditional raw export; agree contracts early; avoid coupling to office schema. |
| Privacy/consent and retention not approved before launch. | Medium | High | Block launch until confirmed; minimize collected data; document retention as Pending. |
| Candidate access problems (expired links, email delivery). | Medium | Medium | Support manual link delivery; clear error states; administrative reissue process. |
| Instrument imposes fixed item/option order that conflicts with confirmed randomization. | Medium | Medium | Validate with HR before content import (HRQ-31); any exception needs explicit approval. |
| Scoring formula changes after results exist. | Medium | Medium | Version assessment definitions and scoring; retain raw responses; key on stable IDs (D-020). |
| Git history or CI discipline missing. | Low | Medium | Repository initialized with a baseline commit and CI; keep committing small and running `composer ci:check`. |
| Single-person implementation knowledge. | Medium | Medium | Keep docs current; use standard Laravel patterns and tests. |

## 11. Glossary

| Term | Meaning |
| --- | --- |
| DISC | The confirmed assessment framework. Intended output describes personality tendencies and dominant styles. Scores and interpretations require HR's authorized specification (D-022). |
| Question | One statement or descriptive phrase from the shared question bank (e.g. "Gembira/Antusias"). Candidates respond with **M** or **L** for that statement — never both, and never a paired second statement. |
| M (Most) / L (Least) | The two alternative responses to the **same** statement: M = describes the candidate most; L = describes the candidate least. Fixed meanings; not a DISC dimension and not reordered. |
| Question bank | The single shared set of questions for the initial release. Managed by HR through the office dashboard, stored and versioned in this app (D-023, D-024). |
| Question version | An immutable snapshot of question content and its response identifiers. Attempts reference the version shown, so later edits cannot rewrite history. |
| Assessment definition | A versioned set of instructions and question versions used for an attempt. Every candidate receives the complete set; question order is randomized per attempt and persisted (D-020). |
| Invitation | A record with a unique token and a defined access window (**Confirmed**, D-019) that allows a specific candidate to start an assessment. Owned by this app; created from the office dashboard (D-018). |
| Access window vs time limit | The access window governs *when* an invitation can be used. The time limit governs *how long* an attempt may run. Both are confirmed to exist; durations and expiry behaviour are Pending (HRQ-04). |
| Attempt | One candidate's session against an assessment definition. One attempt per invitation (**Confirmed**, D-019); resuming an active attempt is Proposed (HRQ-12). |
| Response | The stored M or L selection for one question version, with timestamps. |
| Finalized result | A submitted, locked attempt snapshot. Detailed DISC scores and interpretations exist only after authorized scoring is integrated (D-022); a raw snapshot is not automatically a scored result. |
| Scoring specification | HR's confidential formula and interpretation rules, supplied as an authorized specification or module. Not held in this repository (D-022, HRQ-32). |
| Mock result | Clearly labelled test data used for layout and integration testing. Never presented as a real candidate result (FR-21). |
| Office dashboard | The existing internal HR application; not part of this repository. Hosts the question-bank UI, invitation creation, and result review. |
