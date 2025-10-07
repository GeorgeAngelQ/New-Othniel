<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class CartDetailService
{
    public function addProductToCart(array $data): CartDetail
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::findOrFail($data['id_cart']);
            $product = Product::findOrFail($data['id_product']);
            $quantity = $data['quantity'];
            $unitPrice = $product->price;
            $subtotal = $unitPrice * $quantity;
            $cartDetail = CartDetail::where('id_cart', $cart->id_cart)
                ->where('id_product', $product->id_product)
                ->first();
            if ($cartDetail) {
                $cartDetail->quantity += $quantity;
                $cartDetail->subtotal = $cartDetail->quantity * $unitPrice;
                $cartDetail->save();
            } else {
                $cartDetail = CartDetail::create([
                    'id_cart' => $cart->id_cart,
                    'id_product' => $product->id_product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);
            }
            $this->updateCartTotal($cart);
            return $cartDetail;
        });
    }
    public function updateCartDetail(CartDetail $cartDetail, array $data): CartDetail
    {
        return DB::transaction(function () use ($cartDetail, $data) {
            if (isset($data['quantity'])) {
                $cartDetail->quantity = $data['quantity'];
                $cartDetail->subtotal = $cartDetail->quantity * $cartDetail->unit_price;
            }
            $cartDetail->save();

            $this->updateCartTotal($cartDetail->cart);
            return $cartDetail;
        });
    }
    public function removeCartDetail(CartDetail $cartDetail): void
    {
        DB::transaction(function () use ($cartDetail) {
            $cart = $cartDetail->cart;
            $cartDetail->delete();
            $this->updateCartTotal($cart);
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
