<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Product;

class OrderDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_order' => Order::factory(),
            'id_product' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 90),
            'unit_price' => $this->faker->randomFloat(2, 1, 500),
            'subtotal' => $this->faker->randomFloat(2, 1, 500)
        ];
    }
}
