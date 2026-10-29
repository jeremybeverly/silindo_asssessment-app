# AGENTS.md

Project-wide instructions for any coding agent (OpenCode, Codex, Cursor, Claude, etc.). Tool-specific configuration must not override this file.

## Project status: decision phase

- The documentation foundation was **approved on 2026-10-28** (Gate G0). The MVP path and system boundaries are confirmed (`docs/DECISIONS.md` D-016).
- HR confirmed the instrument and core flow on **2026-10-29**: DISC framework, one shared question bank, one statement per question with exactly one **M (Most)** or **L (Least)** response, invitation access window, assessment time limit, one attempt per invitation, and a mobile-first candidate experience (`D-019`, `D-021`). HR's scoring rules are **confidential** and not supplied (`D-022`).
- Domain work is still **gated** on the remaining inputs in `docs/HR_OPEN_QUESTIONS.md`: question content, identity fields, result fields, question lifecycle, access/time-limit values, and the authorized scoring contract. The launch date is under renegotiation (`D-017`).
- **Permitted now:** documentation work, scaffold inspection, tooling, and tests that do not encode unresolved policy.
- **Still prohibited:** domain migrations, models, routes, or UI that depend on unresolved requirements; any invented DISC scoring, mapping, threshold, or interpretation; committing real candidate data or secrets. These wait for the named Gate G1 answers, or an explicit decision recorded in `docs/DECISIONS.md`.
- When a domain slice is approved, work in small, tested slices against `docs/REQUIREMENTS.md` and `docs/ROADMAP.md`.

## Read before changing anything

1. `docs/PROJECT_BRIEF.md` — why the project exists and its boundaries.
2. `docs/REQUIREMENTS.md` — confirmed requirements, proposals, non-goals.
3. `docs/DECISIONS.md` — what has been decided and what is still open.
4. `docs/HR_OPEN_QUESTIONS.md` — unanswered HR policy questions.
5. `docs/ROADMAP.md` — current phase, scope cuts, deadline.

## Status labels

Use exactly these labels in documentation and code comments where relevant:

| Label | Meaning |
| --- | --- |
| **Confirmed** | Stated by the project owner/sponsor or verified in the repository. Do not change without written approval. |
| **Proposed** | A recommendation from the engineering side. Not agreed; safe to challenge. |
| **Pending** | Awaiting a decision or external input. Do not silently assume an answer. |
| **Deferred** | Intentionally excluded from the current scope. |

Never upgrade a label without evidence. Record decisions in `docs/DECISIONS.md` with a date and owner.

## Confirmed baseline (verified 2026-10-29)

- Laravel **13.39**, PHP **8.5** runtime (`composer.json` requires `^8.3`), Livewire **4.1**, Flux UI **2.13**, Fortify, Pest **5.3**, Tailwind **4**, Vite.
- Only starter-kit features exist: welcome page, dashboard, authentication (login, registration, password reset, email verification, 2FA, passkeys), settings.
- No assessment domain code, routes, or tables exist.
- The instrument is **DISC**; each question is a single statement answered with exactly one **M (Most)** or **L (Least)**. No paired statements, no A/B/C/D or Likert variants (`D-021`).
- HR's scoring rules are confidential; no formula, mapping, threshold, or interpretation exists in the repository (`D-022`).
- Local development and tests run on **PostgreSQL 18.6** (`assessment_app` for the app, `assessment_app_test` for tests); `.env` is gitignored. `.env.example`, `phpunit.xml`, and CI all target PostgreSQL (D-014, Confirmed 2026-10-28).
- No integration with the office dashboard exists.
- The repository is a git repository as of 2026-10-28 (branch `main`; initial scaffold/docs commit `4a6bcd5`). No remote or branch protection is configured yet. `AGENTS.md` is versioned; tool-specific agent config (`.codex`, `.pi`, `.zed`, `opencode.json`, `boost.json`) stays ignored by the starter `.gitignore`.

## Non-negotiable project rules

1. **Scope boundary.** This is the candidate-facing assessment app only. HR reviews results in the existing office dashboard; do not build an HR dashboard, reporting suite, or admin console here.
2. **Separate data.** Use this application's own PostgreSQL database. Never connect to, query, or share tables with the office application's database.
3. **Do not invent HR policy.** Question content, the confidential scoring formula, DISC mappings, thresholds, interpretations, retention period, consent wording, and invitation rules are HR decisions. If one is needed, stop and record or update a question in `docs/HR_OPEN_QUESTIONS.md`. Never infer DISC output from question wording or the M/L labels (`D-022`).
4. **Respect the M/L format.** One statement per question; the candidate chooses exactly **one** of M or L for that statement. Never add a second statement needing the opposite label, and never substitute multiple-choice or Likert options (`D-021`).
5. **Protect history.** Questions referenced by an attempt are immutable; edits create a new version and removal is by archive (`D-006`, `D-024`).
6. **No real data.** Never put real candidate data, names, emails, employee IDs, assessment responses, secrets, or credentials in the repository, tests, fixtures, screenshots, seeders, or logs. Use factories and fake data.
7. **Data minimization.** Collect and log only what the confirmed requirements need. Avoid PII in application logs, URLs, and error tracking.
8. **Minimal reliable deliverable.** Prefer the smallest tested implementation. Do not add packages, services, or architecture layers without approval.
9. **Documentation stays current.** Update the relevant doc in the same change that alters scope, behavior, a decision, or a risk.

## Engineering conventions

- Follow existing Laravel, Livewire, and Pest conventions; inspect sibling files before creating new ones.
- Scaffold files with `php artisan make:* --no-interaction` and pass explicit options.
- Use explicit parameter types and return types, constructor property promotion, curly braces for all control structures, and PHPDoc array shapes.
- Prefer named routes and the `route()` helper. Use Eloquent API Resources and versioned routes for APIs unless existing conventions differ.
- Validate with Form Requests or Livewire validation; authorize on the server with policies/gates, including inside Livewire actions.
- Target PostgreSQL. Avoid SQLite-only SQL and unsafe assumptions in migrations; prefer portable schema changes.
- Enforce access windows, time limits, attempt limits, and answer validation on the server, never only in the browser (`D-019`).
- Build the candidate UI mobile-first; keep M/L labels unambiguous and allow exactly one selection per statement.
- Keep the existing UI stack: Blade, Livewire, Flux, Tailwind. Do not introduce React/Vue or a new CSS framework.
- Do not create new top-level folders without approval. Stay within the existing structure.
- Run `npm run build` if a frontend change does not appear or Vite reports a missing manifest.

## Testing and quality gates

- Pest is the test framework. Default to feature tests; use `php artisan make:test --pest {name}`.
- Add or update tests for meaningful behavior and failure modes, using model factories. Required coverage: exactly one M/L selection per question, second-attempt refusal, access-window and time-limit enforcement, cross-candidate denial, question-version immutability, and idempotent submission.
- Run the narrowest relevant tests while working (`php artisan test --compact <path>` or `--filter=`). Tests run on PostgreSQL (`assessment_app_test`); `phpunit.xml` pins `APP_ENV` and `DB_DATABASE` via `<server>` so a sourced `.env` cannot redirect the suite at the development database.
- Do not delete tests without approval.
- Before finalizing PHP changes, run `vendor/bin/pint --dirty --format agent`.
- Type checks: `composer types:check` (PHPStan/Larastan, level 7). CI runs `composer ci:check`.

## Documentation discipline

- One decision per entry in `docs/DECISIONS.md`; never rewrite history — mark superseded entries.
- When an HR question is answered, update `docs/HR_OPEN_QUESTIONS.md` and reflect the impact in requirements, decisions, and roadmap.
- A scope change requires updates to `docs/REQUIREMENTS.md` and `docs/ROADMAP.md`.
- Do not create additional documentation files without approval; extend the existing set.

## Tooling

- Standard commands: `composer install`, `npm install`, `npm run dev`, `npm run build`, `php artisan test`, `composer lint`, `composer types:check`.
- Laravel Boost MCP tools are optional helpers (application info, database schema/read-only queries, logs, documentation search). Use them when available, but never make a project rule depend on a specific agent or editor.
- Skills live in `.agents/skills` (and mirrored tool folders). Load the relevant skill before working in that domain.
- If `.ai/rules` is added later, read its index and all matching rules before editing files.
- Never commit `.env` or any credential file.

## Definition of done

A change is done when: the behavior is implemented within the confirmed scope, tests cover it and pass, formatting and type checks pass, affected documentation is updated, no secrets or real candidate data were introduced, and all status labels remain accurate.
