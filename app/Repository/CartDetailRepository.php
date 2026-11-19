<?php

namespace App\Repository;

use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;


class CartDetailRepository
{
    public function addProductToCart(array $data): void
    {
    }

    public function addProductToCartSecond(int $id_user, int $id_product, int $quantity): void
    {

    }
    public function updateCartDetail(CartDetail $cartDetail, array $data): CartDetail
    {
        return DB::transaction(function () use ($cartDetail, $data) {
            if (isset($data['quantity'])) {
                $cartDetail->quantity = $data['quantity'];
                $cartDetail->subtotal = $cartDetail->quantity * $cartDetail->unit_price;
            }
            $cartDetail->save();
            $cart = $cartDetail->cart ?? Cart::find($cartDetail->id_cart);
            if ($cart) {
                $this->updateCartTotal($cart);
            }
            return $cartDetail->refresh();
        });
    }

    public function removeCartDetail(CartDetail $cartDetail): void
    {
        DB::transaction(function () use ($cartDetail) {
            $cart = $cartDetail->cart;
            $cartDetail->delete();
            if ($cart) {
                $this->updateCartTotal($cart);
            }
        });
    }
    public function getCartDetails(int $id_cart): Collection
    {
        return CartDetail::with('product')
            ->where('id_cart', $id_cart)
            ->get();
    }
    private function updateCartTotal(Cart $cart): void
    {
        $total = $cart->cartDetails()->sum('subtotal');
        $cart->update(['total' => $total]);
    }
}
