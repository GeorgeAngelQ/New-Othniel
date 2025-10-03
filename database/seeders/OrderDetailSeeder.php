<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;

class OrderDetailSeeder extends Seeder
{
    public function run(): void
    {
        Order::all()->each(function ($order) {
            OrderDetail::factory(3)->create([
                'id_order' => $order->id_order,
                'id_product' => Product::inRandomOrder()->first()->id_product,
            ]);
        });
    }
}
