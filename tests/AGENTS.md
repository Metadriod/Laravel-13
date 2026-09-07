# Laravel 13 Test Rules

These rules apply under `tests/` in addition to the root Database and Environment Safety rules.

- Use the test framework already configured by the Laravel 13 project: Pest, PHPUnit, or both.
- Reuse `Tests\TestCase`, existing helpers, traits, factories, datasets, producers, authentication helpers, and fixtures.
- Put HTTP/API behavior in feature tests and isolated domain/service/helper behavior in unit tests when isolation is meaningful.
- For changed endpoints, cover the successful path plus relevant validation, unauthenticated, unauthorized/forbidden, missing scoped resource, duplicate/conflict, and edge cases.
- Use Laravel HTTP assertions and database assertions instead of manually parsing low-level responses when suitable.
- Fake mail, notifications, queues, events, storage, time, and external HTTP calls unless the integration itself is intentionally under test.
- Use `Http::fake()` or the project's existing fake/client abstraction for external HTTP dependencies.
- Automated tests must never contact production/staging APIs, send real email/SMS/push notifications, write to real cloud storage, or mutate shared databases.
- Never assume `.env.testing` proves isolation. Verify the actual running environment and resolved database connection/host/name.
- `RefreshDatabase`, `DatabaseMigrations`, `DatabaseTruncation`, migrations, seeders, truncation, and any database-writing test require the explicit approval defined in the root instructions before execution.
- For browser/Dusk tests, do not use an in-memory SQLite database across browser/application processes; follow the project's isolated browser-test database setup.
- Do not globally disable middleware, policies, throttling, or exception handling merely to make tests pass unless the specific test requires it or the established test architecture intentionally does so.
- Prefer focused tests during implementation, then run the relevant broader suite only when safe and approved if it can write to a database.
- Do not change production behavior solely to satisfy a brittle or incorrect test; verify the intended behavior first.
