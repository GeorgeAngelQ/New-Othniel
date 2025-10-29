<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Services\MercadoPagoService;
use App\Services\CoinGateService;


class PaymentController extends Controller
{
    protected $mercadoPagoService;
    protected $coinGateService;

    public function __construct(MercadoPagoService $mercadoPagoService, CoinGateService $coinGateService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
        $this->coinGateService = $coinGateService;
    }

    public function createPreferenceMercadoPago($id_order)
    {
        $order = Order::with('orderDetails.products', 'users')->findOrFail($id_order);
        $order->payment_method = 'MercadoPago';
        $order->save();
        $service = new MercadoPagoService();
        $response = $service->crearOrden($order);

        return response()->json($response);
    }

    public function createOrderCrypto($id_order)
    {
        $order = Order::with('orderDetails.products', 'users')->findOrFail($id_order);
        $order->payment_method = 'CoinGate';
        $order->save();

        $coingate = new CoinGateService();
        $response = $coingate->createOrder(
            $order->total,
            'Compra en OTH Store',
            'Pago de productos seleccionados',
            $order->users->email ?? 'test@example.com'
        );

        if (isset($response['payment_url'])) {
            return response()->json($response);
        }

        return response()->json([
            'error' => 'No se pudo crear la orden en CoinGate',
            'details' => $response
        ], 400);
    }

    public function success()
    {
        return response()->json(['message' => 'Pago CoinGate completado']);
    }

    public function cancel()
    {
        return response()->json(['message' => 'Pago CoinGate cancelado']);
    }
}
