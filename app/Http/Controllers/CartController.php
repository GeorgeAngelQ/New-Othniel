<?php

namespace App\Http\Controllers;

use App\Filters\CartFilter;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\CartCollection;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $filter = new CartFilter();
        $queryItems = $filter->transform($request);
        $includeUsers = $request->query('includeUsers');
        $includeCartDetails = $request->query('includeCartDetails');
        $carts = Cart::where($queryItems);
        if ($includeUsers) {
            $carts = $carts->with('users');
        }
        if ($includeCartDetails) {
            $carts = $carts->with('cartDetails');
        }
        $data = new CartCollection($carts->paginate()->appends($request->query()));
        return response()->json([
            'status' => 'success',
            'message' => 'Carts retrieved successfully',
            'data' => $data,
            'error' => null
        ], 200);
    }
    public function create()
    {

    }
    public function store(StoreCartRequest $request)
    {

    }
    public function show(Request $request, Cart $cart)
    {
        $includeUsers = $request->query('includeUsers');
        $includeCartDetails = $request->query('includeCartDetails');
        if ($includeUsers) {
            $cart->load('users');
        }
        if ($includeCartDetails) {
            $cart->load('cartDetails');
        }
        $data = new CartResource($cart);
        return response()->json([
        'status' => 'success',
        'message' => 'Cart retrieved successfully',
        'data' => $data,
        'error' => null
        ], 200);
    }
    public function edit(Cart $cart)
    {

    }
    public function update(UpdateCartRequest $request, Cart $cart)
    {

    }
    public function destroy(Cart $cart)
    {

    }
}
