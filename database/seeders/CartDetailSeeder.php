<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;

class CartDetailSeeder extends Seeder
{
    public function run(): void
    {
        $carts = Cart::all();
        $products = Product::all();

        foreach ($carts as $cart) {
            $selectedProducts = $products->random(rand(1, 4));
            $total = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $subtotal = $product->price * $quantity;

                CartDetail::factory()->create([
                    'id_cart' => $cart->id_cart,
                    'id_product' => $product->id_product,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $cart->update(['total' => $total]);
        }
    }
}
