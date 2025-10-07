<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Product;

class OrderDetailFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $price = $product->price;

        return [
            'id_order' => Order::factory(),
            'id_product' => $product->id_product,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $quantity * $price,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
