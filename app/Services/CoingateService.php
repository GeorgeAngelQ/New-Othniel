<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Order;
use Exception;

class CoinGateService
{
    protected $apiUrl;
    protected $authToken;

    public function __construct()
    {
        $this->apiUrl = config('services.coingate.api_url', 'https://api-sandbox.coingate.com/v2');
        $this->authToken = config('services.coingate.auth_token');
    }

    public function createPayment(Order $order)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->authToken,
                'Accept' => 'application/json',
            ])->post("{$this->apiUrl}/orders", [
                'order_id' => $order->id,
                'price_amount' => $order->total,
                'price_currency' => 'USD',
                'receive_currency' => 'BTC',
                'callback_url' => route('coingate.webhook'),
                'cancel_url' => route('payment.cancel'),
                'success_url' => route('payment.success'),
                'title' => 'Compra en Othniel',
                'description' => 'Pago de pedido #' . $order->id,
            ]);

            if ($response->failed()) {
                throw new Exception('Error en la creación del pago CoinGate: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
