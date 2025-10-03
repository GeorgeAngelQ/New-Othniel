<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            CartSeeder::class,
            CartDetailSeeder::class,
            OrderSeeder::class,
            OrderDetailSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
