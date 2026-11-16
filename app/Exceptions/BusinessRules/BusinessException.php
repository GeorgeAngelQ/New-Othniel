<?php

namespace App\Exceptions\BusinessRules;

use App\Exceptions\ErrorType;
use DomainException;

final class BusinessException extends DomainException
{
    public function __construct(string $message, private readonly array $context=[])
    {
        parent::__construct($message);
    }

    public function errorType(): ErrorType
    {
        return ErrorType::BUSINESS;
    }

    public function context(): array
    {
        return $this->context;
    }
}
