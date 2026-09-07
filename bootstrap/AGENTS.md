# Laravel 13 Bootstrap Rules

These rules apply under `bootstrap/` in addition to the repository root instructions.

- Treat `bootstrap/app.php` as the primary Laravel 13 application configuration point for route loading, middleware configuration, and exception rendering/reporting when the repository follows the default modern skeleton.
- Use `Application::configure(...)` and the existing fluent configuration chain; extend the current configuration instead of creating legacy kernel/handler files.
- Configure route loading with the existing `withRouting(...)` pattern. Preserve current web/API/console/health route registration and prefixes.
- Configure middleware through the existing `withMiddleware(...)` closure using aliases, groups, append/prepend operations, trusted proxies/hosts, or priority only when required.
- Configure exception behavior through the existing `withExceptions(...)` closure. Preserve API JSON rendering behavior and production-safe error responses.
- Laravel 13 applications may intentionally render JSON for `api/*` requests even when the client omits an `Accept: application/json` header; do not remove that behavior unless explicitly requested.
- Do not duplicate middleware aliases or exception rendering in multiple locations.
- Do not register package/service-provider behavior in `bootstrap/app.php` when the package or project already has a dedicated provider/configuration mechanism.
- Keep `bootstrap/cache` generated artifacts out of manual source edits.
- Never weaken trusted-proxy, CORS, authentication, CSRF, cookie, exception, or rate-limiting behavior merely to solve a local tunnel/client issue.
