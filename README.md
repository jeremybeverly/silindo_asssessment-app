# Assessment App

Candidate-facing online DISC workplace personality assessment for HRD.

> **Status: foundation complete; domain decisions pending.** The documentation set was approved on 2026-10-28 (Gate G0) and updated 2026-10-29 with HR confirmations (DISC, one-statement M/L format, access window, time limit, one attempt, mobile-first). No assessment features are implemented. Domain work waits on the remaining Gate G1 inputs (question content, identity fields, result fields) and the authorized scoring contract; the launch date is under renegotiation — see `docs/ROADMAP.md`.

## What this application is

- A separate Laravel application with its **own PostgreSQL database** (**Confirmed**).
- The place where candidates take the DISC assessment on a **mobile-first** interface: one statement per question, exactly one **M (Most)** or **L (Least)** response (**Confirmed**, D-021).
- The owner of question data and question rules, invitation records, token validation, attempts, responses, scoring, and result finalization (**Confirmed** boundary, D-022, D-023).
- Source of truth for candidate responses and finalized assessment results (**Confirmed** boundary).
- A system that hands finalized results to the existing internal office dashboard for HR review (**Confirmed need**; mechanism Pending).
- Scoring is deferred: HR's rules are confidential and the authorized specification has not been provided (**Confirmed deferral**, D-022). No DISC scores or interpretations are invented here.

## What this application is not

- Not an HR dashboard or reporting tool. HR works in the office application, including question-bank management.
- Not connected directly to the office application's database.
- Not an owner of HR policy: assessment content, scoring, retention, and invitation delivery are HR decisions.
- Not a home for confidential scoring rules; only an authorized implementation or contract is integrated (D-022).
- Not assuming the office application already supports the required APIs; that capability is unverified (`HRQ-29`).

Integration with the office application covers three concerns (contracts not finalized):

1. **Question bank** — the office dashboard (the HR interface) performs question CRUD through this app's authenticated API (`D-023`).
2. **Invitation lifecycle** — the office dashboard requests invitation creation and receives the candidate link, plus revocation and status (`D-018`).
3. **Result handoff** — finalized results move to the office dashboard (`D-004`; raw-response export fallback is conditional on HR approval of its fields and sufficiency, `HRQ-08` / `HRQ-30`).

## Documentation

| File | Purpose |
| --- | --- |
| `AGENTS.md` | Working rules for any coding agent and contributor. Read first. |
| `docs/PROJECT_BRIEF.md` | Problem, users, scope, system context, constraints, risks. |
| `docs/REQUIREMENTS.md` | Confirmed vs proposed requirements, non-goals, acceptance criteria. |
| `docs/ROADMAP.md` | Phased plan, decision gates, and scope cuts (launch date under renegotiation). |
| `docs/DECISIONS.md` | Decision log with status, impact, and review trigger. |
| `docs/HR_OPEN_QUESTIONS.md` | Unanswered HR questions, blocking vs non-blocking, safe fallbacks. |

Status labels used throughout: **Confirmed**, **Proposed**, **Pending**, **Deferred**.

## Current technical state

| Area | State | Status |
| --- | --- | --- |
| Framework | Laravel 13.39, PHP 8.5 runtime | Confirmed |
| UI | Livewire 4, Flux UI 2, Tailwind 4, Vite | Confirmed |
| Auth | Laravel Fortify (starter kit: login, registration, 2FA, passkeys) | Confirmed scaffold |
| Database | PostgreSQL 18.6 locally (`assessment_app`); tests on `assessment_app_test`; CI provisions `postgres:18` | Confirmed (D-014, 2026-10-28) |
| Assessment domain | None (no models, migrations, or routes) | Confirmed |
| Instrument | DISC; one shared bank; one statement per question; M/L only | Confirmed (D-021) |
| Scoring | Deferred behind an authorized interface; no formula in repo | Confirmed deferral (D-022) |
| Office integration | None | Confirmed |
| Git repository | Initialized on `main`; initial commit `4a6bcd5` | Confirmed (2026-10-28) |

The scaffold's authentication and settings screens are starter-kit code, not product requirements. Candidate access is invitation-only (**Confirmed**, D-005). Whether the starter kit's public registration/login routes are disabled or removed is still **Proposed** (D-015) and must be resolved before launch.

## Local development (existing scaffold only)

Prerequisites: PHP 8.3+, Composer, Node.js 22+, and a running PostgreSQL 18 instance with two databases: `assessment_app` (development) and `assessment_app_test` (tests), owned by the role in `.env`. The test database must exist before running the suite.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
```

Useful checks:

```bash
php artisan test --compact
composer lint
composer types:check
```

Do not add migrations, dependencies, or configuration changes for unresolved requirements. Database configuration targets PostgreSQL (D-014, applied 2026-10-28); domain work still waits for Gate G1.

## Working agreement

1. Read `AGENTS.md` and the docs above before changing code.
2. Do not invent HR policy, scoring logic, pass thresholds, retention rules, or deployment details. Record unknowns as questions.
3. Keep candidate data and credentials out of the repository and logs.
4. Update documentation whenever scope or a decision changes.
5. Build the smallest reliable slice, with tests, against the agreed MVP.

## Launch date

The original confirmed target was **31 October 2026**. On 2026-10-28 the sponsor initiated a launch-date renegotiation (`docs/DECISIONS.md` D-017); the new date is **Pending** (`HRQ-28`). The cut lines and phase order in `docs/ROADMAP.md` still apply.
