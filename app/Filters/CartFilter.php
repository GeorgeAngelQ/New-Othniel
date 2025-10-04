<?php

namespace App\Filters;
use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class CartFilter extends ApiFilter{
    protected $safeParams = [
        'idUser' => ['eq'],
        'total' => ['eq', 'lt', 'gt', 'lte', 'gte'],
        'status' => ['eq','ne'],
    ];

    protected $columnMap = [
        'id_user' => 'idUser'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>=',
    ];
}
