<?php

namespace App\Http\Controllers;

use App\Filters\CartFilter;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\CartCollection;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

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
        return $this->successResponse($data, 'Carts retrieved successfully');
    }
    public function create()
    {

    }
    public function store(StoreCartRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $data['id_user'] = $user->id_user;
        $cart = Cart::create($data);
        return $this->successResponse(new CartResource($cart), 'Cart created successfully', 201);
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
        return $this->successResponse($data, 'Cart retrieved successfully');
    }
    public function edit(Cart $cart)
    {

    }
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        $cart->update($request->validated());
        return $this->successResponse($cart, 'Cart updated successfully');
    }
    public function destroy(Request $request, Cart $cart)
    {
        $user = $request->user();
        if ($cart->id_user !== $user->id_user) {
            return $this->errorResponse('You do not have permission to delete this item', 403);
        }
        $cart->delete();
        return $this->successResponse(null, 'Cart item deleted successfully');
    }
    public function clear(Request $request)
    {
        $user = $request->user();
        Cart::where('id_user', $user->id_user)->delete();
        return $this->successResponse(null, 'Cart cleared successfully');
    }
}
