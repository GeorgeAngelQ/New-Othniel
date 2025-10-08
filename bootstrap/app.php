<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
                    'data' => null,
                    'error' => class_basename($e),
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Endpoint not found',
                    'data' => null,
                    'error' => class_basename($e),
                ], 404);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'data' => null,
                    'error' => class_basename($e),
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'data' => null,
                    'error' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden',
                    'data' => null,
                    'error' => $e->getMessage(),
                ], 403);
            }
        });

        $exceptions->render(function (QueryException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error',
                    'data' => null,
                    'error' => config('app.debug') ? $e->getMessage() : 'Error executing query',
                ], 500);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Method Not Allowed',
                    'data' => null,
                    'error' => $e->getMessage(),
                ], 405);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Internal Server Error',
                    'data' => null,
                    'error' => config('app.debug') ? $e->getMessage() : 'Unexpected error occurred',
                ], 500);
            }
        });
    })->create();
