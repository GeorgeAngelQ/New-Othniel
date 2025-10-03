<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Filters\ProductFilter;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filter = new ProductFilter();
        $queryItems = $filter->transform($request);
        $users = Product::where($queryItems);
        return new ProductCollection($users->paginate()->appends($request->query()));
    }
    public function create()
    {

    }
    public function store(StoreProductRequest $request)
    {

    }
    public function show(Product $product)
    {

    }
    public function edit(Product $product)
    {

    }
    public function update(UpdateProductRequest $request, Product $product)
    {

    }
    public function destroy(Product $product)
    {

    }
}
