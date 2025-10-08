<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
class ShoppingFlowTest extends TestCase
{
    /** @test */
    public function user_can_create_cart_add_products_and_checkout(): void
    {
        $this->artisan('migrate');

        $user = User::factory()->create();
        $products = Product::factory(3)->create();

        $cart = Cart::factory()->create([
            'id_user' => $user->id_user,
            'status' => 'active',
        ]);

        $total = 0;
        foreach ($products as $product) {
            $qty = rand(1, 3);
            $subtotal = $qty * $product->price;
            $total += $subtotal;

            CartDetail::create([
                'id_cart' => $cart->id_cart,
                'id_product' => $product->id_product,
                'quantity' => $qty,
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
            ]);
        }

        $cart->update(['total' => $total, 'status' => 'completed']);

        $order = Order::create([
            'id_user' => $user->id_user,
            'id_cart' => $cart->id_cart,
            'total' => $cart->total,
            'status' => 'paid',
            'payment_method' => 'credit_card',
        ]);

        foreach ($cart->CartDetails as $detail) {
            OrderDetail::create([
                'id_order' => $order->id_order,
                'id_product' => $detail->id_product,
                'quantity' => $detail->quantity,
                'price' => $detail->unit_price,
                'subtotal' => $detail->subtotal,
            ]);
        }

        $payment = Payment::create([
            'id_order' => $order->id_order,
            'payment_method' => $order->payment_method,
            'amount' => $order->total,
            'status' => 'completed',
            'reference_transaction' => 'TXN-TEST-1234',
        ]);

        $this->assertDatabaseHas('carts', ['id_cart' => $cart->id_cart, 'status' => 'completed']);
        $this->assertDatabaseHas('orders', ['id_order' => $order->id_order, 'total' => $cart->total]);
        $this->assertDatabaseHas('payments', ['id_order' => $order->id_order, 'status' => 'completed']);
        $this->assertEquals($cart->CartDetails->count(), $order->OrderDetails->count());
        $this->assertEquals($cart->total, $order->total);
    }
}
