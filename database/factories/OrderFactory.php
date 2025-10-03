<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['Pendiente', 'Pagado', 'Cancelado', 'Enviado']);
        $payment_method = null;

        switch ($status) {
            case 'Pendiente':
                $payment_method = null;
                break;
            case 'Pagado':
                $payment_method = $this->faker->randomElement(['MercadoPago', 'Paypal', 'Coingate']);
                break;
            case 'Cancelado':
                $payment_method = null;
                break;
            case 'Enviado':
                $payment_method = $this->faker->randomElement(['MercadoPago', 'Paypal', 'Coingate']);
                break;
        }
        return [
            'id_user'=> User::factory(),
            'date' => $this->faker->date(),
            'total' => $this->faker->randomFloat(2, 20, 1000),
            'status' => $status,
            'payment_method' => $payment_method,
        ];
    }
}
