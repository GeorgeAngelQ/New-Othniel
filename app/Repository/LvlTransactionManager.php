<?php

namespace App\Repository;

use App\Contracts\TransactionManager;
use Illuminate\Support\Facades\DB;
use Closure;

class LvlTransactionManager implements TransactionManager
{
    public function transaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }
}

