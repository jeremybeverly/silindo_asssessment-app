# Project Brief

**Status:** Approved 2026-10-28 (Gate G0) · **Date:** 2026-10-28 · **Launch date:** under renegotiation (original target 2026-10-31)

Status labels: **Confirmed**, **Proposed**, **Pending**, **Deferred**.

## 1. Problem

**Status:** Confirmed (project brief).

HRD needs candidates to complete a psychometric assessment online and needs HR to review the finalized result in the existing internal office dashboard. Today there is no candidate-facing assessment application and no agreed data handoff between such an application and the office system.

## 2. Purpose

**Status:** Confirmed (project brief).

Deliver a minimal, reliable candidate assessment application — original target end of October 2026, launch date now under renegotiation (D-017) — that:

1. Lets invited candidates complete the assessment online.
2. Captures their responses and produces a finalized result.
3. Makes that finalized result available to the existing office dashboard for HR review.

## 3. Users and systems

| Actor / system | Role | Status |
| --- | --- | --- |
| Candidate | External person who receives an invitation and completes the assessment. | Confirmed |
| HR reviewer | Reviews finalized results in the existing office dashboard, not in this app. | Confirmed |
| HR / assessment owner | Owns assessment content, scoring, thresholds, retention, and invitation policy. | Confirmed role; policies Pending |
| Assessment App | This Laravel application, with its own PostgreSQL database. | Confirmed |
| Office dashboard | Existing Laravel + PostgreSQL internal application used by HR. | Confirmed |
| Integration | Two concerns: invitation lifecycle (office dashboard requests creation/revocation/status and receives the candidate link) and finalized-result handoff. | Proposed; contract Pending; office-side capability unverified (HRQ-29) |

**Proposed assumption:** an HR administrator prepares invitations using an internal process or a simple command/import; no HR administration UI is built in this application.

## 4. Scope

### In scope

- Candidate-facing assessment flow. **Confirmed**
- Storage of assessment definitions, invitations, attempts, responses, and results in this application's database. **Proposed**
- Ownership of invitation records, tokens, validation, attempts, responses, and result finalization in this app. **Confirmed** boundary
- Invitation lifecycle integration with the office dashboard: creation on request, revocation, status retrieval. **Proposed**
- Finalization of results. **Proposed** (definition Pending)
- Handoff of finalized results to the office dashboard. **Confirmed need**; mechanism Pending
- Basic operational safeguards: authentication of integration traffic, audit timestamps, error handling, backups. **Proposed**

### Out of scope (Confirmed or Proposed)

| Item | Status | Note |
| --- | --- | --- |
| HR dashboard, result review UI, analytics | **Confirmed out** | Exists in the office application. |
| Psychometric instrument design, question authoring, scoring science | **Confirmed out** | HR responsibility; content and formula Pending. |
| Candidate accounts, self-registration, social login | **Confirmed out** | Invitation-only access confirmed (D-005, 2026-10-28); disabling/removing the starter kit's public auth routes is Proposed (D-015) and a launch blocker. |
| Proctoring, webcam, identity verification | **Deferred** | Not requested; would add cost and privacy burden. |
| ATS/payroll integration, multi-tenant support | **Deferred** | No requirement. |
| Localization beyond English | **Deferred** | Confirm with HR if candidates require other languages. |
| Native mobile apps | **Deferred** | Responsive web only. |

## 5. System context

```text
Candidate browser
      ^
      | HTTPS, invitation link (link created via office dashboard, D-018, Proposed)
      |
Assessment App (Laravel, PostgreSQL)
  owns invitations, tokens, attempts, responses, result finalization
      |                                    ^
      | finalized results (Proposed)       | invitation create / revoke / status (Proposed, HRQ-29)
      v                                    |
Office Dashboard (existing Laravel, PostgreSQL) — the HR interface
```

- The two applications have **separate databases** (Confirmed).
- The office dashboard is the only HR interface (Confirmed).
- The assessment app owns invitation records, token validation, attempts, responses, and finalization (Confirmed boundary).
- Integration has two concerns: invitation lifecycle (office → app, **Proposed**; office-side capability not assumed, HRQ-29) and result handoff (app → office, **Proposed**; contract Pending).
- The office dashboard remains the system of record for HR review, not necessarily for assessment responses.

## 6. Constraints

| Constraint | Status |
| --- | --- |
| Deadline: end of October 2026 (31 Oct 2026). | Original target; renegotiation initiated 2026-10-28 (D-017), new date Pending |
| Minimal, reliable deliverable; three calendar days remain from the documentation date. | Confirmed / derived |
| Assessment app is a separate Laravel application with its own PostgreSQL database. | Confirmed |
| Existing office app is Laravel + PostgreSQL. | Confirmed |
| Assessment type, questions, scoring formula, pass thresholds, retention, invitation workflow are not finalized. | Confirmed |
| Deployment environment and integration contract are not finalized. | Confirmed |
| No real candidate data or credentials may be used during development. | Confirmed project rule |

## 7. Assumptions (Proposed unless marked otherwise)

| ID | Assumption | Impact if wrong |
| --- | --- | --- |
| A-01 | Candidates access the assessment through a unique invitation link, without creating an account. **Confirmed via D-005 (2026-10-28).** | Would require authentication, registration, and password-reset scope. |
| A-02 | One assessment instrument is launched at the deadline; multiple instruments come later. | Schema and UI must support multiple definitions sooner. |
| A-03 | One attempt **total** per invitation; an active attempt is resumable after refresh or browser closure. **Owner preference 2026-10-28 (D-019); Proposed pending HR confirmation.** | Retake, reset, and revocation rules change the data model and support process. |
| A-04 | The assessment is delivered in English. | Adds translation and content-versioning scope. |
| A-05 | Raw responses can be stored before the scoring formula is finalized. | If raw storage is not acceptable, scoring must be decided before schema work. |
| A-06 | A CSV/JSON export is an acceptable temporary handoff if the API is not ready, **provided HR approves the export fields and confirms the available data suffices for review (HRQ-30)**. | Manual HR work or a hard dependency on the office team. |
| A-07 | The office team can expose or consume an authenticated endpoint. | Integration may need a different mechanism or manual import. |
| A-08 | Candidates use modern desktop or mobile browsers with a stable connection. | Offline/low-bandwidth support becomes necessary. |

## 8. Recommendations

| ID | Recommendation | Status |
| --- | --- | --- |
| R-01 | Freeze a thin MVP: invitation → consent → assessment → autosave → submit → finalize → export/API. | Proposed |
| R-02 | Store raw responses with assessment/question version identifiers; add scoring only after HR approves the formula. | Proposed |
| R-03 | Build an authenticated CSV/JSON export first and the API second, so HR is never blocked by the integration — viable only if HR approves the export fields and confirms the available data is sufficient for review (HRQ-30). | Proposed |
| R-04 | Do not build an HR UI in this application. | Proposed |
| R-05 | Keep policy values (time limit, attempt rules, retention) configurable, not hard-coded. | Proposed |
| R-06 | Collect the minimum candidate identity fields needed by HR; confirm them before schema work. | Proposed |
| R-07 | Treat this repository as the only source for assessment data; never read the office database directly. | Proposed |

## 9. Success criteria

Proposed definition of success for the deadline:

1. An invited candidate can open the link, understand the consent/instructions, complete the assessment, and submit without engineering support.
2. Answers survive a page refresh or brief connection loss during an active attempt.
3. A submitted attempt is locked and cannot be altered by the candidate.
4. HR can retrieve a finalized result for the candidate through the agreed handoff (API or documented export).
5. No candidate can access another candidate's attempt or result.
6. The flow works on a modern mobile browser and a desktop browser.
7. All automated tests pass; no secrets or real candidate data are present in the repository.

## 10. Risks

| Risk | Likelihood | Impact | Mitigation |
| --- | --- | --- | --- |
| HR decisions (content, scoring, invitation) arrive after the deadline. | High | High | Set decision gate; ship raw-response capture; defer scoring/API with export fallback. |
| Original 31 October deadline left roughly three days; launch date now under renegotiation. | High | High | Re-baseline the schedule once the new date is agreed (HRQ-28); enforce cut lines; keep the MVP thin. |
| Office-side development needed for both integrations (invitation APIs and result handoff); capability unverified. | Medium | High | Pilot fallbacks: CLI/CSV invitation creation and conditional raw export; agree contracts early; avoid coupling to office schema. |
| Privacy/consent and retention not approved before launch. | Medium | High | Block launch until confirmed; minimize collected data; document retention as Pending. |
| Candidate access problems (expired links, email delivery). | Medium | Medium | Support manual link delivery; clear error states; administrative reissue process. |
| Instrument imposes fixed item/option order that conflicts with confirmed randomization. | Medium | Medium | Validate with HR before content import (HRQ-31); any exception needs explicit approval. |
| Scoring formula changes after results exist. | Medium | Medium | Version assessment definitions and scoring; retain raw responses; key on stable IDs (D-020). |
| No git history or CI discipline at project start. | Medium | Medium | Initialize repository and protect the main branch before feature work. |
| Single-person implementation knowledge. | Medium | Medium | Keep docs current; use standard Laravel patterns and tests. |

## 11. Glossary

| Term | Meaning |
| --- | --- |
| Assessment definition | A versioned set of instructions, questions, and configuration used for an attempt. Every candidate receives the complete question set; question and answer-option order are randomized per attempt and persisted (Confirmed, D-020). |
| Invitation | A record with a unique token and a configurable expiry (**Proposed**, D-019) that allows a specific candidate to start an assessment. Owned by this app; creation is intended to be requested from the office dashboard (**Proposed**, D-018). |
| Invitation expiry vs attempt time limit | Invitation expiry governs whether an attempt may be started. Any attempt time limit or deadline is a separate, not-yet-defined policy (HRQ-04); no timer or auto-submit is assumed. |
| Attempt | One candidate's session against an assessment definition, including answers and timing. One attempt **total** per invitation (**Proposed**, D-019); resumable after refresh or browser closure. |
| Response | A candidate's answer to one question, with timestamps. |
| Finalized result | An attempt that has been submitted and locked. May be a raw (unscored) snapshot pending HRQ-30; it is not automatically a scored psychometric result, and scoring is not invented here. |
| Office dashboard | The existing internal HR application; not part of this repository. |
