<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        User::where('role', 'user')->get()->each(function ($user) {
            Order::factory(2)->create([
                'id_user' => $user->id_user,
            ]);
        });
    }
}
