<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $paidOrders = Order::whereIn('status', ['paid', 'shipped', 'delivered'])->get();

        foreach ($paidOrders as $order) {
            Payment::factory()->create([
                'id_order' => $order->id_order,
                'payment_method' => $order->payment_method,
                'amount' => $order->total,
                'status' => 'completed',
                'reference_transaction' => strtoupper(fake()->bothify('TXN###??')),
            ]);
        }
    }
}
