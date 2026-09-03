<?php

use App\Enums\ApiErrorCode;
use App\Enums\AppEnvironment;
use App\Http\Middleware\ApiKeyPermission;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\EnsureEmailIsVerifiedApi;
use App\Http\Middleware\EnsureUserIsActivated;
use App\Http\Middleware\EnsureWebhooksAreEnabled;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\LowerCaseQueryParam;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\ValidateSignature;
use App\Http\Middleware\VerifyCsrfToken;
use App\Providers\PolymorphMapProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);

        $middleware->group('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            SubstituteBindings::class,
            ForceJsonResponse::class,
            EnsureUserIsActivated::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.session' => AuthenticateSession::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => RequirePassword::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,
            'verified.api' => EnsureEmailIsVerifiedApi::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'lowercase_query' => LowerCaseQueryParam::class,
            'api_key_permission' => ApiKeyPermission::class,
            'enabled.webhooks' => EnsureWebhooksAreEnabled::class,
            'block.deactivated' => EnsureUserIsActivated::class,
        ]);
    })
    ->withProviders([
        PolymorphMapProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            if (! $request->is('api/*')) {
                return null; // fallback to default handler
            }

            return match (true) {
                $e instanceof NotFoundHttpException,
                $e instanceof MethodNotAllowedHttpException => response()->json([
                    'success' => false,
                    'message' => 'Route not found',
                    'error_code' => ApiErrorCode::UNKNOWN_ROUTE,
                ], Response::HTTP_NOT_FOUND),

                $e instanceof ThrottleRequestsException => response()->json([
                    'success' => false,
                    'message' => 'Too many requests',
                    'error_code' => ApiErrorCode::RATE_LIMIT,
                ], Response::HTTP_TOO_MANY_REQUESTS),

                $e instanceof ValidationException => response()->json([
                    'success' => false,
                    'message' => 'A validation error has occurred',
                    'error_code' => ApiErrorCode::VALIDATION,
                    'errors' => collect($e->errors())->map(
                        fn ($messages, $field) => ['field' => $field, 'messages' => $messages]
                    )->values(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY),

                $e instanceof AuthenticationException => response()->json([
                    'success' => false,
                    'message' => 'Authentication error',
                    'error_code' => ApiErrorCode::UNAUTHORIZED,
                ], Response::HTTP_UNAUTHORIZED),

                $e instanceof UnauthorizedException,
                $e instanceof AuthorizationException,
                $e instanceof HttpException && $e->getStatusCode() === Response::HTTP_FORBIDDEN => response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_code' => ApiErrorCode::UNAUTHORIZED,
                ], Response::HTTP_FORBIDDEN),

                $e instanceof ModelNotFoundException => response()->json([
                    'success' => false,
                    'message' => class_basename($e->getModel()).' not found',
                    'error_code' => ApiErrorCode::RESOURCE_NOT_FOUND,
                ], Response::HTTP_NOT_FOUND),

                $e instanceof PostTooLargeException => response()->json([
                    'success' => false,
                    'message' => 'Request is too large',
                    'error_code' => ApiErrorCode::PAYLOAD_TOO_LARGE,
                ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE),

                default => tap(response()->json(
                    app()->environment(AppEnvironment::PRODUCTION->value)
                        ? ['message' => 'An unknown error has occurred', 'error_code' => ApiErrorCode::SERVER]
                        : [
                            'message' => $e->getMessage(),
                            'error_code' => ApiErrorCode::SERVER,
                            'stack_trace' => $e->getTraceAsString(),
                        ],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                ), fn () => Log::error($e->getMessage(), ['stack_trace' => $e->getTraceAsString()])),
            };
        });
    })
    ->withCommands([__DIR__.'/../app/Console/Commands'])
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('sanctum:prune-expired --hours=24')
            ->daily()
            ->onOneServer();

        $schedule->command('mfa:prune-expired-attempts')
            ->daily()
            ->onOneServer();
    })
    ->create();
