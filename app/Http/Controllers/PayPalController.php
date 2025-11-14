<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    protected $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    public function createOrder(Request $request)
    {
        $amountUSD = $request->amount_usd;

        if (!$amountUSD || $amountUSD <= 0) {
            return response()->json(['error' => 'Monto inválido'], 422);
        }

        $order = $this->paypal->createOrder($amountUSD);

        return response()->json($order);
    }

    public function captureOrder(Request $request)
    {
        $orderId = $request->order_id;

        if (!$orderId) {
            return response()->json(['error' => 'Falta order_id'], 422);
        }

        $capture = $this->paypal->captureOrder($orderId);

        return response()->json($capture);
    }
}
