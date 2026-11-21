<?php

namespace App\Contracts;

use Closure;

interface TransactionManager
{
    public function transaction(Closure $callback): mixed;
}
