<?php

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Middleware\ResolveLocale;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', [
            ResolveLocale::class,
        ]);

        $middleware->appendToGroup('api', [
            ResolveLocale::class,
        ]);

        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'optional.auth' => \App\Http\Middleware\OptionalAuth::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('dashboard*')) {
                return route('dashboard.login');
            }

            return route('frontend.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $responder = new class
        {
            use ApiResponse;
        };

        $jsonGuard = static fn ($request): bool => $request->expectsJson()
            || $request->wantsJson()
            || $request->is('api/*');

        $statusMap = [
            400 => ['BAD_REQUEST', 'errors.bad_request'],
            401 => ['UNAUTHENTICATED', 'errors.unauthenticated'],
            403 => ['FORBIDDEN', 'errors.forbidden'],
            404 => ['NOT_FOUND', 'errors.not_found'],
            405 => ['METHOD_NOT_ALLOWED', 'errors.method_not_allowed'],
            409 => ['CONFLICT', 'errors.conflict'],
            422 => ['VALIDATION_ERROR', 'errors.validation_failed'],
            429 => ['TOO_MANY_REQUESTS', 'errors.too_many_requests'],
            500 => ['SERVER_ERROR', 'errors.server_error'],
            503 => ['SERVICE_UNAVAILABLE', 'errors.service_unavailable'],
        ];

        $exceptions->render(function (Throwable $throwable, $request) use ($responder, $jsonGuard, $statusMap) {
            if (! $jsonGuard($request)) {
                return null;
            }

            // Validation errors
            if ($throwable instanceof ValidationException) {
                // Get first error message or use generic message
                $errors = $throwable->errors();
                $firstError = ! empty($errors) ? reset($errors)[0] : null;
                $message = $firstError ?: __('errors.validation_failed');

                return $responder->errorResponse(
                    $message,
                    $throwable->errors(),
                    $throwable->status
                );
            }

            // Authentication errors
            if ($throwable instanceof AuthenticationException) {
                return $responder->errorResponse(
                    __('errors.unauthenticated'),
                    null,
                    401
                );
            }

            // Authorization errors
            if ($throwable instanceof AuthorizationException) {
                return $responder->errorResponse(
                    $throwable->getMessage() ?: __('errors.forbidden'),
                    null,
                    403
                );
            }

            // Not Found errors
            if ($throwable instanceof ModelNotFoundException || $throwable instanceof NotFoundHttpException) {
                return $responder->errorResponse(
                    __('errors.not_found'),
                    null,
                    404
                );
            }

            // Method Not Allowed errors
            if ($throwable instanceof MethodNotAllowedHttpException) {
                return $responder->errorResponse(
                    __('errors.method_not_allowed'),
                    null,
                    405
                );
            }

            // Rate Limiting errors
            if ($throwable instanceof ThrottleRequestsException) {
                return $responder->errorResponse(
                    __('errors.too_many_requests'),
                    null,
                    429
                );
            }

            // Duplicate Entry errors
            if ($throwable instanceof QueryException && str_contains($throwable->getMessage(), '1062')) {
                return $responder->errorResponse(
                    __('errors.conflict'),
                    null,
                    409
                );
            }

            // Generic errors
            $status = $throwable instanceof HttpExceptionInterface
                ? $throwable->getStatusCode()
                : 500;

            [$code, $messageKey] = $statusMap[$status] ?? $statusMap[500];

            return $responder->errorResponse(
                $throwable->getMessage() ?: __($messageKey),
                null,
                $status
            );
        });
    })->create();
