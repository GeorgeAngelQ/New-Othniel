<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use App\Filters\ProductFilter;
use App\Http\Resources\ProductResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $filter = new ProductFilter();
        $queryItems = $filter->transform($request);
        $includeOrderDetails = $request->query('includeOrderDetails');
        $products = Product::where($queryItems);
        if ($includeOrderDetails) {
            $products = $products->with('orderDetails');
        }
        $data = new ProductCollection($products->paginate()->appends($request->query()));
        return $this->successResponse($data, 'Products retrieved successfully');
    }
    public function create()
    {

    }
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        return $this->successResponse($product, 'Product created successfully', 201);
    }
    public function show(Request $request, Product $product)
    {
        $includeOrderDetails = $request->query('includeOrderDetails');
        if ($includeOrderDetails) {
            $product->load('orderDetails');
        }
        $data = new ProductResource($product);
        return $this->successResponse($data, 'Product retrieved successfully');
    }
    public function edit(Product $product)
    {

    }
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return $this->successResponse($product, 'Product updated successfully');
    }
    public function destroy(Product $product)
    {

    }
}
