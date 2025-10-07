<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\CartDetail;

class OrderDetailSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $cartDetails = CartDetail::where('id_cart', $order->id_cart)->get();

            foreach ($cartDetails as $detail) {
                OrderDetail::factory()->create([
                    'id_order' => $order->id_order,
                    'id_product' => $detail->id_product,
                    'quantity' => $detail->quantity,
                    'price' => $detail->unit_price,
                    'subtotal' => $detail->subtotal,
                ]);
            }
        }
    }
}
