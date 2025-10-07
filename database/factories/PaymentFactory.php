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
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash']),
            'reference_transaction' => strtoupper(fake()->bothify('TXN###??')),
            'amount' => fake()->randomFloat(2, 50, 1000),
            'status' => fake()->randomElement(['completed', 'pending', 'failed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
