<?php

use App\Http\Middleware\EnsureJsonEnvelope;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [
            EnsureJsonEnvelope::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Keep one response shape for every API failure: { success, code, message, data }.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            [$status, $message] = match (true) {
                $e instanceof AuthenticationException => [401, 'Unauthenticated'],
                $e instanceof AuthorizationException => [403, 'Forbidden'],
                $e instanceof ValidationException => [422, 'Validation failed'],
                $e instanceof HttpExceptionInterface => [
                    $e->getStatusCode(),
                    Response::$statusTexts[$e->getStatusCode()] ?? 'Error',
                ],
                default => [500, 'Server error'],
            };

            return response()->json([
                'success' => false,
                'code' => $status,
                'message' => $message,
                // Surface field errors so the dashboard can show them.
                'data' => $e instanceof ValidationException ? $e->errors() : null,
            ], $status);
        });
    })->create();
