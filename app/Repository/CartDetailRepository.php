<?php

namespace App\Repository;

use App\Models\CartDetail;

class CartDetailRepository
{

    public function findByCartIdAndProductId(int $id_cart, int $id_product): ?CartDetail
    {
        return CartDetail::where('cart_details.id_cart', $id_cart)
            ->where('cart_details.id_product', $id_product)
            ->join('carts', 'cart_details.id_cart', '=', 'carts.id_cart')
            ->where('carts.status', 'active')
            ->select('cart_details.*')
            ->first();
    }

    public function create(array $data): CartDetail
    {
        return CartDetail::create($data);
    }

    public function update(CartDetail $cartDetail, array $data): CartDetail
    {
        return CartDetail::where('id_cart_detail', $cartDetail->id_cart_detail)->update($data);
    }

    public function delete(int $id_cart_detail): void
    {
        CartDetail::where('id_cart_detail', $id_cart_detail)->delete();
    }
}
