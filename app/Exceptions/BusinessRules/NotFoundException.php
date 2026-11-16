<?php

namespace App\Exceptions\BusinessRules;

use App\Exceptions\ErrorType;
use DomainException;

final class NotFoundException extends DomainException
{
    public function __construct(
        string $message = 'Recurso no encontrado'
    ) {
        parent::__construct($message);
    }

    public function errorType(): ErrorType
    {
        return ErrorType::NOT_FOUND;
    }
}
