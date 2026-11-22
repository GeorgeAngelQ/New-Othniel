<?php

namespace App\Exceptions\BadRequest;

use App\Exceptions\ErrorType;
use RuntimeException;
use Throwable;

class BadRequestException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly array $errors = [],
        Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }

    public function errorType(): ErrorType
    {
        return ErrorType::BAD_REQUEST;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
