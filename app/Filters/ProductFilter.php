<?php

namespace App\Filters;
use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class ProductFilter extends ApiFilter{
    protected $safeParams = [
        'name' => ['eq'],
        'email' => ['eq'],
        'description' => ['eq'],
        'price' => ['eq', 'lt', 'gt','lte','gte'],
        'stock' => ['eq', 'lt', 'gt','lte','gte'],
        'image_url' => ['eq'],
        'created_at' => ['eq', 'lt', 'gt','lte','gte'],
    ];

    protected $columnMap = [
        'created_at' => 'createdAt',
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>='
    ];
}
