<?php

namespace App\Http\Controllers;

use App\Filters\CartFilter;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\CartCollection;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Models\Cart;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function index(Request $request)
    {
        $filters = $request->only(['id_user', 'status']);
        $carts = $this->cartService->getAllCarts($filters);

        return $this->successResponse(
            new CartCollection($carts),
            'Carts retrieved successfully'
        );
    }
    public function getOrCreateCart(Request $request)
    {
        $user = $request->user();

        $cart = Cart::where('id_user', $user->id_user)
            ->where('status', 'active')
            ->with('cartDetails.product')
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'id_user' => $user->id_user,
                'total' => 0,
                'status' => 'active',
            ]);
        }

        return $this->successResponse($cart, 'Cart retrieved or created successfully');
    }

    public function store(StoreCartRequest $request)
    {
        $cart = $this->cartService->createCart($request->validated());

        return $this->successResponse(
            new CartResource($cart),
            'Cart created successfully',
            201
        );
    }
    public function show(int $id_cart)
    {
        $cart = $this->cartService->getCartById($id_cart);

        return $this->successResponse(
            new CartResource($cart),
            'Cart retrieved successfully'
        );
    }
    public function edit(Cart $cart) {}
    public function update(UpdateCartRequest $request, Cart $cart)
    {
        $updatedCart = $this->cartService->updateCart($cart, $request->validated());
        return $this->successResponse(
            new CartResource($updatedCart),
            'Cart updated successfully'
        );
    }
    public function destroy(Request $request, Cart $cart)
    {
        $this->cartService->deleteCart($cart);
        return $this->successResponse(null, 'Cart deleted successfully');
    }
}
