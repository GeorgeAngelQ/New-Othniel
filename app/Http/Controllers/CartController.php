<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Traits\ApiResponseTrait;

class CartController extends Controller
{
    use ApiResponseTrait;

    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    
    public function getMyCart()
    {
        $id_user = auth('sanctum')->id();
        $cart = $this->cartService->getMyCart($id_user);
        return $this->successResponse($cart, 'Cart is retrieved successfully');
    }

    public function update(UpdateCartRequest $request)
    {
        $validated = $request->validated();
        $id_user = auth('sanctum')->id();
        $updatedCart = $this->cartService->updateCart($id_user, $validated['status']);
        return $this->successResponse(
            new CartResource($updatedCart),
            'Cart updated successfully'
        );
    }

    public function destroy()
    {
        $id_user = auth('sanctum')->id();
        $this->cartService->deleteCart($id_user);
        return $this->successResponse(null, 'Cart deleted successfully',204);
    }
}
