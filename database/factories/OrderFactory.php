<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Cart;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'id_cart' => Cart::factory(),
            'date' => fake()->date(),
            'total' => fake()->randomFloat(2, 50, 500),
            'status' => fake()->randomElement(['pending', 'paid', 'shipped', 'delivered', 'cancelled']),
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
