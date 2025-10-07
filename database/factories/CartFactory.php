<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'total' => 0,
            'status' => fake()->randomElement(['active', 'completed', 'abandoned']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
