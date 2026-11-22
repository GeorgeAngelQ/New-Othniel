<?php

namespace App\Exceptions;

use Throwable;
use App\Exceptions\BusinessRules\BusinessException;
use App\Exceptions\BusinessRules\NotFoundException;

use App\Exceptions\BadRequest\BadRequestException;
use Illuminate\Validation\ValidationException;

use Illuminate\Auth\AuthenticationException;

use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionMapper
{
    public static function map(Throwable $e): array
    {
        return match (true) {

            // ================= DOMINIO =================
            $e instanceof BusinessException => [
                'status' => $e->errorType()->status(),
                'error_code' => $e->errorType()->value,
                'message' => $e->getMessage(),
                'records' => $e->context(),
            ],

            $e instanceof NotFoundException => [
                'status' => $e->errorType()->status(),
                'error_code' => $e->errorType()->value,
                'message' => $e->getMessage(),
            ],

            // ================= VALIDACIÓN =================
            $e instanceof BadRequestException => [
                'status' => $e->errorType()->status(),
                'error_code' => $e->errorType()->value,
                'message' => __('validation.bad_request'),
                'records' => $e->errors(),
            ],
            $e instanceof ValidationException => [
                'status' => ErrorType::VALIDATION->status(),
                'error_code' => ErrorType::VALIDATION->value,
                'message' => __('validation.failed'),
                'fields' => $e->errors(),
            ],


            // ================= AUTENTICACIÓN =================
            $e instanceof AuthenticationException => [
                'status' => ErrorType::UNAUTHORIZED->status(),
                'error_code' => ErrorType::UNAUTHORIZED->value,
                'message' => __('auth.unauthorized'),
            ],


            // ================= AUTORIZACIÓN =================
            $e instanceof AuthorizationException => [
                'status' => ErrorType::FORBIDDEN->status(),
                'error_code' => ErrorType::FORBIDDEN->value,
                'message' => __('auth.forbidden'),
            ],


            // ================= HTTP / FRAMEWORK =================
            $e instanceof NotFoundHttpException => [
                'status' => ErrorType::NOT_FOUND->status(),
                'error_code' => ErrorType::NOT_FOUND->value,
                'message' => __('general.not_found'),
            ],
            $e instanceof ModelNotFoundException => [
                'status' => ErrorType::NOT_FOUND->status(),
                'error_code' => ErrorType::NOT_FOUND->value,
                'message' => __('general.not_found'),
            ],
            $e instanceof MethodNotAllowedHttpException => [
                'status' => ErrorType::METHOD_NOT_ALLOWED->status(),
                'error_code' => ErrorType::METHOD_NOT_ALLOWED->value,
                'message' => __('general.method_not_allowed'),
            ],
            $e instanceof HttpExceptionInterface => (function () use ($e) {
                $type = ErrorType::fromHttpStatus($e->getStatusCode());
                return [
                    'status' => $type->status(),
                    'error_code' => $type->value,
                    'message' => __('general.'. strtolower($type->value)),
                    'headers' => $e->getHeaders()
                ];
            })(),

            // ================= FALLBACK =================
            default => [
                'status' => ErrorType::INTERNAL->status(),
                'error_code' => ErrorType::INTERNAL->value,
                'message' => __('general.internal_error'),
            ],

        } + self::debug($e);
    }

    private static function debug(Throwable $e): array
    {
        if (!config('app.debug')){
            return [];
        }
        return [
            '_debug' => [
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
            ]
        ];
    }
}
