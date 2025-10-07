<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartDetailRequest;
use App\Http\Requests\UpdateCartDetailRequest;
use App\Models\CartDetail;
use App\Services\CartDetailService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Http\Resources\CartDetailCollection;
use App\Http\Resources\CartDetailResource;

class CartDetailController extends Controller
{
    use ApiResponseTrait;

    protected CartDetailService $cartDetailService;

    public function __construct(CartDetailService $cartDetailService)
    {
        $this->cartDetailService = $cartDetailService;
    }
    public function index(Request $request, $id_cart)
    {
        $details = $this->cartDetailService->getCartDetails($id_cart);
        return $this->successResponse(new CartDetailCollection($details), 'Cart details retrieved successfully');
    }
    public function store(StoreCartDetailRequest $request)
    {
        $detail = $this->cartDetailService->addProductToCart($request->validated());
        return $this->successResponse(new CartDetailResource($detail), 'Product added to cart successfully', 201);
    }
    public function update(UpdateCartDetailRequest $request, CartDetail $cartDetail)
    {
        $updatedDetail = $this->cartDetailService->updateCartDetail($cartDetail, $request->validated());
        return $this->successResponse(new CartDetailResource($updatedDetail), 'Cart detail updated successfully');
    }
    public function destroy(CartDetail $cartDetail)
    {
        $this->cartDetailService->removeCartDetail($cartDetail);
        return $this->successResponse(null, 'Cart detail deleted successfully');
    }
}
