<?php

namespace App\Exceptions;

enum ErrorType: string
{
    case BAD_REQUEST = 'BAD_REQUEST';
    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';
    case NOT_FOUND = 'NOT_FOUND';
    case METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';
    case BUSINESS = 'BUSINESS';
    case VALIDATION = 'VALIDATION';
    case TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS';
    case DATABASE = 'DATABASE';
    case INTERNAL = 'INTERNAL';
    case SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';

    public function status(): int
    {
        return match ($this) {
            self::BAD_REQUEST => 400,
            self::UNAUTHORIZED => 401,
            self::FORBIDDEN => 403,
            self::NOT_FOUND => 404,
            self::METHOD_NOT_ALLOWED => 405,
            self::BUSINESS => 409,
            self::VALIDATION => 422,
            self::TOO_MANY_REQUESTS => 429,
            self::DATABASE => 500,
            self::INTERNAL => 500,
            self::SERVICE_UNAVAILABLE => 503
        };
    }

    public static function fromHttpStatus(int $status): self
    {
        return match (true) {
            $status === 401 => self::UNAUTHORIZED,
            $status === 403 => self::FORBIDDEN,
            $status === 404 => self::NOT_FOUND,
            $status === 405 => self::METHOD_NOT_ALLOWED,
            $status === 409 => self::BUSINESS,
            $status === 422 => self::VALIDATION,
            $status === 429 => self::TOO_MANY_REQUESTS,
            $status === 500 => self::DATABASE,
            $status === 503 => self::SERVICE_UNAVAILABLE,
            $status >= 400  && $status < 500 => self::BAD_REQUEST,

            default => self::INTERNAL
        };
    }
}

