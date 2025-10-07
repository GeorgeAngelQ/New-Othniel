<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use App\Models\Product;

class CartDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_cart' => Cart::factory(),
            'id_product' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->randomFloat(2, 1, 100),
            'subtotal' => $this->faker->randomFloat(2, 1, 500),
        ];
    }
}
