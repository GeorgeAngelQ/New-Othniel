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
        return new CartCollection($carts->paginate()->appends($request->query()));
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
        return new CartResource($cart);
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
