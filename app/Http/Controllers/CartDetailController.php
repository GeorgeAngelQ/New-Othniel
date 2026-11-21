<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartDetailRequest;
use App\Http\Requests\UpdateCartDetailRequest;
use App\Models\CartDetail;
use App\Services\CartDetailService;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\CartDetailResource;

use App\Services\CartService;

class CartDetailController extends Controller
{
    use ApiResponseTrait;

    private CartDetailService $cartDetailService;
    private CartService $cartService;

    public function __construct(CartDetailService $cartDetailService, CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->cartDetailService = $cartDetailService;
    }

    public function store(StoreCartDetailRequest $request)
    {
        $validated = $request->validated();
        $id_user = auth('sanctum')->id();
        $cart = $this->cartService->findOrCreateCartByUserId($id_user);
        $detail = $this->cartDetailService->addProductToCart($cart->id_cart, $validated['id_product'], $validated['quantity']);
        $this->cartService->recalculateTotal($cart);
        return $this->successResponse(new CartDetailResource($detail), 'Product added to your cart successfully', 201);
    }

    public function update(UpdateCartDetailRequest $request)
    {
        $validated = $request->validated();
        $id_user = auth('sanctum')->id();
        $cart = $this->cartService->findOrCreateCartByUserId($id_user);
        $updatedDetail = $this->cartDetailService->updateQuantity($cart->id_cart, $validated['id_product'], $validated['quantity']);
        return $this->successResponse(new CartDetailResource($updatedDetail), 'Quantity modified successfully');
    }

    public function destroy(CartDetail $cartDetail)
    {
        $this->cartDetailService->delete($cartDetail->id_cart, $cartDetail->id_product);
        return $this->successResponse(null, 'Product has been deleted from your cart successfully', 204);
    }
}
