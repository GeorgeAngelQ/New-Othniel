<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\MercadoPagoService;

class PaymentController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    public function createPreferenceMercadoPago($id_cart)
    {
        $cart = Cart::with(['CartDetails.product', 'users'])->findOrFail($id_cart);
        $preference = $this->mercadoPagoService->crearOrden($cart);

        if (isset($preference->id) && isset($preference->init_point)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Preferencia creada correctamente',
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
                'id_preference' => $preference->id
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No se pudo crear la preferencia',
            'details' => $preference
        ], 500);
    }
}
