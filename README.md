# Assessment App

Candidate-facing online psychometric assessment application for HRD.

> **Status: foundation approved; decisions pending.** The documentation set was approved on 2026-10-28 (Gate G0). No assessment features are implemented. Feature work waits on the P0 HR questions (Gate G1), and the launch date is under renegotiation — see `docs/ROADMAP.md`.

## What this application is

- A separate Laravel application with its **own PostgreSQL database** (**Confirmed**).
- The place where candidates take an online assessment (**Confirmed**).
- The owner of invitation records, token validation, attempts, responses, and result finalization (**Confirmed** boundary).
- Intended to be the source of truth for candidate responses and finalized assessment results (**Proposed**).
- A system that hands finalized results to the existing internal office dashboard for HR review (**Confirmed need**; mechanism Pending).

## What this application is not

- Not an HR dashboard or reporting tool. HR works in the office application.
- Not a psychometric instrument, scoring policy, or retention policy owner. Those are HR decisions.
- Not connected directly to the office application's database.
- Not assuming the office application already supports invitation APIs; that capability is unverified (`HRQ-29`).

Integration with the office application covers two separate concerns (both **Proposed**; contracts not finalized):

1. **Invitation lifecycle** — the office dashboard (the HR interface) requests invitation creation and receives the candidate link; revocation and status retrieval included (`D-018`; office-side capability unverified, `HRQ-29`).
2. **Result handoff** — finalized results move to the office dashboard (`D-004`; raw-response export fallback is conditional on HR approval of its fields and sufficiency, `HRQ-08` / `HRQ-30`).

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
| Assessment domain | None | Confirmed |
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
