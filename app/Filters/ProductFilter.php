<?php

namespace App\Filters;
use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class ProductFilter extends ApiFilter{
    protected $safeParams = [
        'name' => ['eq'],
        'email' => ['eq'],
        'description' => ['eq'],
        'price' => ['eq','lt', 'gt','lte','gte'],
        'stock' => ['eq','lt', 'gt','lte','gte'],
        'imageUrl' => ['eq'],
        'createdAt' => ['eq', 'lt', 'gt','lte','gte'],
    ];

    protected $columnMap = [
        'image_url' => 'imageUrl',
        'created_at' => 'createdAt'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>='
    ];
}
