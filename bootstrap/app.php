<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use App\Http\Middleware\RoleMiddleware;
use App\Exceptions\ApiExceptionMapper;
use App\Exceptions\ApiErrorResponse;
use App\Exceptions\BadRequest\BadRequestException;
use App\Exceptions\BusinessRules\BusinessException;
use App\Exceptions\BusinessRules\NotFoundException;
use Psr\Log\LogLevel;

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

        $exceptions->dontReport([NotFoundException::class]);
        $exceptions->level(BusinessException::class, LogLevel::INFO);
        $exceptions->level(BadRequestException::class, LogLevel::WARNING);

        $exceptions->context(fn () => [
            'request_id' => request()->attributes->get('request_id'),
            'user_id' => auth('sanctum')->id(),
            'route' => request()->route()?->getName() ?? request()->path(),
            'method' => request()->method(),
        ]);

        $exceptions->dontReportDuplicates();

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (Throwable $e, Request $request) {

            if (!$request->expectsJson() && !$request->is('api/*')) {
                return null;
            }

            $mapped = ApiExceptionMapper::map($e);

            return ApiErrorResponse::make($mapped);
        });

    })
    ->create();
