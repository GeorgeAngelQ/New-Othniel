<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $users= User::where('role', 'user')->get();

        foreach($users as $user) {
            Cart::factory()->create([
                'id_user' => $user->id_user,
            ]);
        };
    }
}
