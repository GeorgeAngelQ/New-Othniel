<?php

namespace App\Repository;

use App\Models\Cart;
class CartRepository {

    public function findById(int $id_cart): ?Cart
    {
        return Cart::with('cartDetails.product')->find($id_cart);
    }

    public function findByUserId(int $id_user): ?Cart
    {
        return Cart::where('id_user', $id_user)->where('status', 'active')->first();
    }

    public function findByUserIdWithDetails(int $id_user): ?Cart
    {
        return Cart::with('cartDetails.product')->where('id_user', $id_user)->where('status', 'active')->first();
    }

    public function createCart(array $data): Cart
    {
        return Cart::create($data);
    }

    public function updateCart(int $id_cart, array $data): Cart
    {
        Cart::where('id_cart', $id_cart)->update($data);
        return $this->findById($id_cart);
    }
}
