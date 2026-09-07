# Laravel 13 Application and API Rules

These rules apply under `app/` in addition to the repository root instructions.

## Architecture

- Keep controllers thin: accept the request, authorize, delegate business behavior, and return a response.
- Use Form Requests for non-trivial validation and request authorization.
- Use API Resources or the project's established response layer for stable external JSON contracts.
- Place meaningful business logic in the project's established service, action, manager, domain, or equivalent layer when separation is useful; do not create wrapper classes for trivial Eloquent operations.
- Prefer constructor injection and Laravel's service container.
- Reuse existing interfaces/contracts and bind implementations only when an abstraction has multiple implementations, a meaningful boundary, or an established project pattern.
- Prefer PHP enums and Laravel casts for stable domain values when consistent with the project.
- Use framework features available in Laravel 13 rather than introducing compatibility workarounds for older Laravel versions.

## Controllers, Requests, and Resources

- Controllers should not contain large validation arrays, complex query construction, multi-table workflows, or reusable business rules.
- Put request normalization in the Form Request lifecycle when appropriate, including trimming, case normalization, canonical IDs, and boolean/date normalization.
- Use `authorize()` or the project's policy/middleware approach for request-level authorization.
- Prefer `validated()` or `safe()` data over `$request->all()` for write operations.
- Preserve existing validation error shapes and public field names unless intentionally changing the API contract.
- Never expose sensitive/hidden model attributes through raw model serialization.
- Prevent accidental lazy-loading/N+1 behavior in API Resources by eager loading required relationships and using conditional resource helpers when appropriate.

## Authentication and Authorization

- Use the authentication system already installed in the Laravel 13 project. For token APIs, use Sanctum when the project uses Sanctum; do not introduce JWT, Passport, or another parallel auth mechanism without an explicit requirement.
- Use policies, gates, token abilities, permission middleware, or the project's established authorization layer instead of scattered role-name checks.
- Never accept privileged roles, permissions, ownership, tenant IDs, or field-office scope from untrusted input without server-side authorization.
- Scope protected model queries to the authenticated user's permitted tenant/organization/field office/resource domain before resolving or returning records.
- Treat route-model binding as lookup convenience, not authorization; still enforce policies/scopes.
- Do not reveal account/resource existence through inconsistent error behavior when the existing security design intentionally avoids enumeration.

## API Response Standards

- Preserve the project's existing JSON envelope and error contract.
- For new APIs without an established custom envelope, prefer clear JSON, API Resources/Resource Collections, and conventional HTTP semantics instead of inventing unnecessary wrapper layers.
- Use appropriate HTTP status codes: 200 for successful reads/updates, 201 for creation, 204 for successful no-content responses, 401 for unauthenticated, 403 for forbidden, 404 for missing scoped resources, 409 for true conflicts, and 422 for validation failures where applicable.
- Use `GET` for reads, `POST` for creation/non-idempotent commands, `PATCH` for partial updates, `PUT` for replacement semantics, and `DELETE` for deletion.
- Do not silently rename response fields, change types/nullability/date formats, alter pagination metadata, or change nesting for existing consumers.
- Use ISO-8601-compatible date/time serialization and preserve the project's timezone rules.

## Eloquent and Query Rules

- Prefer Eloquent relationships and the query builder; use raw SQL only when justified, parameterized, and reviewed for portability/security.
- Protect against N+1 queries with explicit eager loading for endpoint response graphs.
- Avoid unbounded `Model::all()` on growing datasets; paginate, cursor-paginate, chunk, or lazily iterate as appropriate.
- Enforce reasonable maximum page sizes for request-controlled pagination.
- Use deterministic ordering for paginated endpoints.
- Whitelist allowed sort/filter fields; never pass arbitrary request-controlled column names into `orderBy`, raw expressions, or dynamic SQL.
- Keep mass assignment intentional. Never blindly call `Model::create($request->all())`, `update($request->all())`, or equivalent with untrusted fields.
- Use database constraints for duplicate-sensitive invariants in addition to application validation.
- Use model casts for booleans, dates, encrypted values, collections, and enums when they clarify the domain.

## Transactions, Events, and Jobs

- Use `DB::transaction()` for multi-write operations that must succeed or fail atomically.
- Keep remote HTTP calls, email sending, and slow I/O outside long database transactions whenever possible.
- Dispatch queued work/events after commit when downstream behavior must not observe rolled-back data.
- Make queued jobs retry-safe/idempotent where practical and use explicit retry/backoff/timeout behavior for important jobs.
- Do not catch broad exceptions merely to suppress failures. Catch only for meaningful recovery, translation, cleanup, domain context, or deliberate reporting.

## Files, External APIs, and Webhooks

- Use Laravel's filesystem abstraction or the project's storage service rather than hardcoded filesystem paths.
- Generate server-controlled filenames/paths for uploads; validate MIME/type, extension where relevant, size, and authorization before storing.
- Keep private files private unless public access is explicitly required; use temporary/signed URLs or controlled download endpoints where appropriate.
- Use Laravel's HTTP client for external requests unless the project already has a dedicated client abstraction.
- Configure connect/request timeouts and deliberate retry behavior for external services; never disable TLS verification in committed code.
- Handle non-2xx HTTP responses explicitly; do not assume Laravel's HTTP client throws automatically for all failures.
- Authenticate webhooks using the provider/project's signature, API key, OAuth, mTLS, or equivalent mechanism and validate replay/idempotency requirements.
- Use unique event IDs, nonces, timestamps, database constraints, or domain-specific idempotency keys for duplicate-sensitive webhook/command processing.

## Logging, Cache, Notifications, and Queues

- Preserve centralized exception handling configured by the Laravel 13 application.
- Log actionable context only and minimize personal/sensitive data.
- Use namespaced cache keys that include tenant/user/context boundaries where necessary to prevent cross-user leakage.
- Invalidate or version cached data when writes make it stale.
- Reuse existing queue names, connection configuration, retry/backoff conventions, events, listeners, mail, and notification channels.
- Do not send external mail/SMS/push or hit external APIs from automated tests unless explicitly testing that integration in an isolated environment.
