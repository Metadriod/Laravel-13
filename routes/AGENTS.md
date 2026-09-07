# Laravel 13 Route Rules

These rules apply under `routes/` in addition to the repository root instructions.

- Use the Laravel 13 route files actually registered by `bootstrap/app.php`.
- For API-first applications, keep public API routes in the existing API route entrypoint and feature-specific route files if the project already splits them.
- If API routing/Sanctum has not been installed in a new Laravel 13 project, do not run `php artisan install:api` or add Sanctum unless the user explicitly asks for API authentication/setup.
- Preserve route prefixes, versioning such as `/api/v1`, names, domains, middleware order, throttles, bindings, and public URLs unless explicitly changing the API contract.
- Prefer controller actions over large route closures for non-trivial API behavior.
- Group routes by feature/controller/prefix only when it improves clarity and matches the project convention.
- Attach authentication, authorization, verification, tenant/field-office scoping, feature flags, and throttling at the established route/middleware layer.
- Never make a protected endpoint public merely to solve client integration.
- Never place state-changing behavior behind `GET`.
- Treat route-model binding as lookup convenience; authorization must still be enforced.
- Avoid duplicate, ambiguous, or overly broad parameterized routes that shadow specific endpoints.
- Treat route removal, renaming, HTTP-method changes, prefix changes, and version changes as breaking API changes unless explicitly approved.
- Update OpenAPI/Swagger/Postman or other maintained API documentation when route contracts change.
