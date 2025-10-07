<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 10, 300),
            'stock' => fake()->numberBetween(5, 100),
            'image_url' => fake()->imageUrl(400, 400, 'products', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
