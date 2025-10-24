<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MercadoPagoService;

class PaymentController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    public function createPreferenceMercadoPago($id_order)
    {
        $order = Order::with('orderDetails.products', 'users')->findOrFail($id_order);

        $service = new MercadoPagoService();
        $response = $service->crearOrden($order);

        return response()->json($response);
    }

}
