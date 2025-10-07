<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Cart::factory(fake()->numberBetween(1, 2))->create([
                'id_user' => $user->id_user,
                'status' => fake()->randomElement(['active', 'completed']),
            ]);
        }
    }
}
