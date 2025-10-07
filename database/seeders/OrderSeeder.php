<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $completedCarts = Cart::where('status', 'completed')->get();

        foreach ($completedCarts as $cart) {
            Order::factory()->create([
                'id_user' => $cart->id_user,
                'id_cart' => $cart->id_cart,
                'total' => $cart->total,
                'status' => fake()->randomElement(['paid', 'shipped', 'delivered']),
                'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            ]);
        }
    }
}
