<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->hasCart(1)
            ->hasOrder(4)
            ->create();
        User::factory()
            ->count(34)
            ->hasCart(1)
            ->hasOrder(2)
            ->create();
        User::factory()
            ->count(20)
            ->hasOrder(4)
            ->create();
        User::factory()
            ->count(12)
            ->create();
    }
}
