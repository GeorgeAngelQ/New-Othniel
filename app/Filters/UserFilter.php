<?php

namespace App\Filters;
use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class UserFilter extends ApiFilter{
    protected $safeParams = [
        'name' => ['eq'],
        'email' => ['eq'],
        'createdAt' => ['eq', 'lt', 'gt'],
    ];

    protected $columnMap = [
        'created_at' => 'createdAt',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'gt' => '>'
    ];
}
