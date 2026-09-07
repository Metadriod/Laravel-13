# Laravel 13 Database Rules

These rules apply under `database/` in addition to the root Database and Environment Safety rules.

## Migrations

- Use Laravel 13 migration/schema APIs and the database driver already configured by the project.
- Prefer additive, backward-compatible migrations.
- Never drop tables, columns, indexes, foreign keys, constraints, or data unless explicitly requested and the impact is understood.
- Preserve existing data. For new required columns on populated tables, plan a safe nullable/default/backfill sequence before enforcing non-null constraints.
- Add foreign keys, indexes, unique constraints, and supported check constraints when they represent real integrity/query requirements.
- Enforce duplicate-sensitive domain rules with database unique constraints in addition to application validation.
- Name indexes/constraints explicitly when doing so improves portability, rollback reliability, or operational clarity.
- Make `down()` intentional; do not write a destructive rollback merely for symmetry when rollback would cause unacceptable data loss.
- Consider table size, locking, index-build cost, and deployment sequencing before altering large production tables.
- Never execute migrations until the root database-safety approval and target-verification requirements are satisfied.

## Seeders

- Keep reference/lookup seeders deterministic and idempotent where practical using appropriate upsert/update-or-create patterns.
- Separate production/reference seed data from local demo/test data.
- Never embed real credentials, passwords, API keys, private tokens, or real personal data in seeders or dumps.
- Preserve stable IDs only when the domain/API contract actually depends on those IDs.
- Keep large reference datasets in dedicated import/dump resources when that matches the repository's convention.
- Never execute seeders against a shared or non-disposable database without the explicit approval required by the root instructions.

## Factories

- Use Laravel model factories for synthetic test data.
- Prefer realistic but fictional defaults and valid relationships.
- Use factory states for meaningful variants instead of duplicating setup logic across tests.
- Avoid factory callbacks that unexpectedly call external systems or create unrelated side effects.

## Database Command Safety

- Do not run `migrate`, `migrate:fresh`, `migrate:refresh`, `migrate:reset`, `db:wipe`, `db:seed`, database-writing Tinker code, restoration/replay commands, or database-writing test suites until the root safety requirements are satisfied.
- Resolve and report the actual Laravel environment, connection, host, and database name before approval for a mutating command.
- Abort when configuration caching, environment loading, Docker/Sail indirection, or connection resolution makes the target uncertain.
