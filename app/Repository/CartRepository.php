<?php

namespace App\Repository;

use App\Models\Cart;
class CartRepository {
    public function findById(int $id): ?Cart
    {
        return Cart::find($id);
    }
    public function findByUserId(int $id_user): ?Cart
    {
        return Cart::where('id_user', $id_user)->where('status', 'active')->first();
    }
    public function getCartById(int $id_cart): ?Cart
    {
        return Cart::with('cartDetails.product')->findOrFail($id_cart);
    }
    public function getCartByUserId(int $id_user): ?Cart
    {
        return Cart::with('cartDetails.product')->where('id_user', $id_user)->where('status', 'active')->first();
    }
    public function createCart(array $data): Cart
    {
        return Cart::create($data);
    }
}
