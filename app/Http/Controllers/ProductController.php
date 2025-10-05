<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Filters\ProductFilter;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filter = new ProductFilter();
        $queryItems = $filter->transform($request);
        $includeOrderDetails = $request->query('includeOrderDetails');
        $products = Product::where($queryItems);
        if ($includeOrderDetails) {
            $products = $products->with('orderDetails');
        }
        return new ProductCollection($products->paginate()->appends($request->query()));
    }
    public function create()
    {

    }
    public function store(StoreProductRequest $request)
    {

    }
    public function show(Request $request, Product $product)
    {
        $includeOrderDetails = $request->query('includeOrderDetails');
        if ($includeOrderDetails) {
            $product->load('orderDetails');
        }
        return new ProductResource($product);
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
