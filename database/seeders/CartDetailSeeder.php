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
        Cart::all()->each(function ($cart) {
            CartDetail::factory(3)->create([
                'id_cart' => $cart->id_cart,
                'id_product' => Product::inRandomOrder()->first()->id_product,
            ]);
        });
    }
}
