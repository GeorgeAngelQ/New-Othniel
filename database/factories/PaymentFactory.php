<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_order' => Order::factory(),
            'payment_method' => $this->faker->randomElement(['MercadoPago', 'Paypal', 'Coingate']),
            'reference_transaction' => $this->faker->uuid(),
            'amount' => $this->faker->randomFloat(2, 20, 1000),
            'status' => $this->faker->randomElement(['completed', 'pending', 'failed']),
        ];
    }
}
