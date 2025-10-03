<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'total' => $this->faker->randomFloat(2, 0, 1000),
            'status' => $this->faker->randomElement(['Activo', 'Cerrado']),
        ];
    }
}
