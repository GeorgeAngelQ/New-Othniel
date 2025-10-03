<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Order::whereIn('status', ['Pagado', 'Enviado'])->get()->each(function ($order) {
            Payment::factory()->create([
                'id_order' => $order->id_order,
                'payment_method' => $order->payment_method,
                'amount' => $order->total,
                'status' => 'completed'
            ]);
        });
    }
}
