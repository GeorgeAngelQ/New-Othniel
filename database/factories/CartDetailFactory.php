<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use App\Models\Product;

class CartDetailFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);

        return [
            'id_cart' => Cart::factory(),
            'id_product' => $product->id_product,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'subtotal' => $quantity * $product->price,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
