<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('payments')->truncate();
        DB::table('order_details')->truncate();
        DB::table('orders')->truncate();
        DB::table('cart_details')->truncate();
        DB::table('carts')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            CartSeeder::class,
            CartDetailSeeder::class,
            OrderSeeder::class,
            OrderDetailSeeder::class,
            PaymentSeeder::class,
        ]);

        $this->command->info(' Base de datos poblada.');
    }
}
