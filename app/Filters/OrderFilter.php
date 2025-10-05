<?php

namespace App\Filters;
use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class OrderFilter extends ApiFilter{
    protected $safeParams = [
        'idUser' => ['eq'],
        'date' => ['eq', 'lt', 'gt', 'lte', 'gte'],
        'total' => ['eq', 'lt', 'gt', 'lte', 'gte'],
        'status' => ['eq','ne'],
        'paymentMethod' => ['eq','ne'],
        'createdAt' => ['eq', 'lt', 'gt', 'lte', 'gte'],
        'updatedAt' => ['eq', 'lt', 'gt', 'lte', 'gte'],
    ];

    protected $columnMap = [
        'id_user' => 'idUser',
        'payment_method' => 'paymentMethod',
        'created_at' => 'createdAt',
        'updated_at' => 'updatedAt',
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
