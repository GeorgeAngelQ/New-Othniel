<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\NotOwnsCartException;

class CartService
{
    public function getOrCreateCartForUser(User $user): Cart
    {
        return Cart::firstOrCreate(
            ['id_user' => $user->id_user, 'status' => 'Activo'],
            ['total' => 0.00]
        );
    }
    public function addItem(User $user, int $productId, int $quantity): CartDetail
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1.');
        }
        return DB::transaction(function () use ($user, $productId, $quantity) {
            $product = Product::where('id_product', $productId)->lockForUpdate()->firstOrFail();
            $existingCart = $this->getOrCreateCartForUser($user);
            $existingDetail = CartDetail::where('id_cart', $existingCart->id_cart)
                ->where('id_product', $productId)
                ->first();
            $newQuantity = ($existingDetail ? $existingDetail->quantity : 0) + $quantity;
            if ($product->stock < $newQuantity) {
                throw new InsufficientStockException('Insufficient stock for product ' . $product->id_product);
            }
            $unitPrice = $product->price;
            if ($existingDetail) {
                $existingDetail->quantity = $newQuantity;
                $existingDetail->subtotal = round($unitPrice * $newQuantity, 2);
                $existingDetail->save();
                $detail = $existingDetail;
            } else {
                $detail = CartDetail::create([
                    'id_cart' => $existingCart->id_cart,
                    'id_product' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => round($unitPrice * $quantity, 2),
                ]);
            }
            $this->recalculateTotal($existingCart);
            return $detail;
        });
    }
    public function updateItem(User $user, int $cartDetailId, int $quantity): CartDetail
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1.');
        }
        return DB::transaction(function () use ($user, $cartDetailId, $quantity) {
            $detail = CartDetail::with('cart')->findOrFail($cartDetailId);
            if ($detail->cart->id_user !== $user->id_user) {
                throw new NotOwnsCartException('You do not own this cart item.');
            }
            $product = Product::where('id_product', $detail->id_product)->lockForUpdate()->firstOrFail();
            if ($product->stock < $quantity) {
                throw new InsufficientStockException('Insufficient stock for product.');
            }
            $detail->quantity = $quantity;
            $detail->subtotal = round($product->price * $quantity, 2);
            $detail->unit_price = $product->price;
            $detail->save();
            $this->recalculateTotal($detail->cart);
            return $detail;
        });
    }
    public function removeItem(User $user, int $cartDetailId): void
    {
        DB::transaction(function () use ($user, $cartDetailId) {
            $detail = CartDetail::with('cart')->findOrFail($cartDetailId);
            if ($detail->cart->id_user !== $user->id_user) {
                throw new NotOwnsCartException('You do not own this cart item.');
            }
            $cart = $detail->cart;
            $detail->delete();

            $this->recalculateTotal($cart);
        });
    }
    public function clearCart(User $user): void
    {
        DB::transaction(function () use ($user) {
            $cart = Cart::where('id_user', $user->id_user)->first();

            if ($cart) {
                $cart->cartDetails()->delete();
                $cart->update(['total' => 0.00]);
            }
        });
    }
    public function recalculateTotal(Cart $cart): Cart
    {
        $total = $cart->cartDetails()->sum('subtotal');
        $cart->total = round((float)$total, 2);
        $cart->save();

        return $cart;
    }
}
