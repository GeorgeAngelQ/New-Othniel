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
    case DATABASE = 'DATABASE';
    case INTERNAL = 'INTERNAL';

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
            self::DATABASE => 500,
            self::INTERNAL => 500,
        };
    }
}

