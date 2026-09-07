# Laravel 13 Repository Instructions

## Scope

- These rules apply to the entire Laravel 13 repository.
- Nested `AGENTS.md` files add directory-specific rules. More specific rules may refine these instructions, but they must never weaken the Git, database, environment, or security protections in this root file.
- Treat the repository as the source of truth. Inspect existing code, `composer.json`, configuration, and nearby implementations before changing behavior.

## Laravel 13 Baseline

- Target Laravel 13 and the PHP version declared by this repository's `composer.json`; do not write compatibility shims for Laravel 9-12 unless explicitly requested.
- Follow the Laravel 13 application structure and APIs already present in the repository.
- Prefer modern Laravel 13 configuration through `bootstrap/app.php` for routing, middleware, and exception behavior when that is how the project is structured.
- Do not recreate legacy `app/Http/Kernel.php`, `app/Exceptions/Handler.php`, or provider-based registration patterns merely to imitate older Laravel versions.
- Use Laravel 13-supported framework APIs and current package APIs; avoid deprecated patterns.
- Never upgrade PHP constraints, Laravel, Symfony components, Composer dependencies, Node dependencies, or lockfiles unless explicitly requested.
- Optional packages such as Sanctum, Passport, Spatie Permission, Horizon, Telescope, Scout, Pennant, Reverb, or project-specific packages must only be used when already installed or explicitly requested.

## Change Discipline

- Inspect similar implementations before creating new code.
- Reuse established controllers, Form Requests, Resources, services/actions/managers, policies, middleware, jobs, events, listeners, notifications, enums, casts, query scopes, filters, helpers, and tests before adding parallel abstractions.
- Keep changes scoped to the requested behavior and avoid unrelated refactors.
- Preserve public API contracts, authentication behavior, authorization/scoping, SPA/mobile clients, webhooks, queues, storage behavior, and configuration defaults unless the task explicitly changes them.
- Prefer additive and backward-compatible changes for existing endpoints and schemas.
- Do not remove working functionality merely to simplify implementation.
- Never commit `.env`, `.env.testing`, credentials, tokens, private keys, local caches, runtime output, or other secrets.

## Security Baseline

- Treat request data, headers, filenames, webhook payloads, route parameters, and external API responses as untrusted input.
- Never log passwords, OTPs, bearer tokens, API keys, cookies, authorization headers, private keys, session identifiers, or secrets.
- Never expose stack traces, SQL, filesystem paths, internal exception details, or secrets in production API responses.
- Do not weaken authentication, authorization, CSRF, CORS, TLS, trusted-proxy, cookie, rate-limit, or validation protections merely to make development easier.
- Never concatenate untrusted data into SQL, shell commands, filesystem paths, raw expressions, or dynamic sort/column clauses.
- Preserve tenant, organization, field-office, user, and resource scoping server-side; never rely only on client-side visibility or IDs.

## Database and Environment Safety

- Never run destructive or database-resetting tests against staging, production, UAT, shared development databases, or any sensitive/shared environment.
- `RefreshDatabase`, `DatabaseMigrations`, `DatabaseTruncation`, `migrate:fresh`, `migrate:refresh`, `db:wipe`, truncation, destructive seed replacement, and equivalent operations are allowed only against a proven isolated disposable local test database.
- Obtain the user's explicit approval before running any automated test or command that can write to, migrate, seed, reset, truncate, restore, replay, or drop a database.
- Before requesting approval, provide the exact command, resolved Laravel environment, database connection, host, database name, expected mutations, isolation checks, and rollback/backup plan.
- Never rely only on `.env.testing` or an assumed connection. Verify the actual runtime environment and resolved database target from the process that will execute the command. Abort on any mismatch, configuration-cache uncertainty, or inability to prove isolation.
- Before deployment or environment-level commands, verify the resolved application environment, database host, and database name exactly match the intended target.
- Do not perform recovery/restoration writes without separate explicit approval; perform recovery experiments in a new database first.
- Read-only diagnostics may proceed when needed but must not expose credentials, secrets, raw personal identifiers, or sensitive records.
- Never run brute-force simulations, repeated authentication attempts, load tests, rate-limit tests, or security scans against staging/production without separate explicit authorization, approved scope, test account/source IP, testing window, and stop condition.

## Git Safety

- Never push commits, merges, rebases, tags, force-pushes, or refs directly to remote `develop` or `main`.
- Never run an equivalent refspec that updates remote `develop` or `main`, including `HEAD:develop` or `HEAD:main`.
- Before every push, verify the current local branch and exact remote destination. Abort if the destination resolves to `develop` or `main`.
- If currently on `develop` or `main`, create or switch to a feature/fix branch before committing or preparing a push.
- Push only to non-protected feature/fix branches and only when the user explicitly asks for the push.
- Promotion to `develop` or `main` must happen through the repository's approved merge-request/pull-request process.

## Laravel 13 Workflow

1. Inspect `composer.json`, installed packages, `bootstrap/app.php`, relevant routes, models, requests, resources, policies, services, and tests.
2. Identify the existing public API contract and authorization/scoping requirements.
3. Make the smallest Laravel 13-native change using existing project abstractions.
4. Add or update focused tests when appropriate.
5. Run only verified non-destructive formatting, static analysis, and tests unless database-safety rules require approval.
6. Before any database-affecting command, verify the actual target and follow Database and Environment Safety.
7. Before any push, verify the branch and destination and follow Git Safety.
8. At handoff, summarize assumptions, changed files, API/schema changes, and verification commands/results.
