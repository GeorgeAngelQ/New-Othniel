<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class CoinGateService
{
    protected $token;
    protected $baseUrl;

    public function __construct()
    {
        $this->token = config('services.coingate.token', env('COINGATE_API_TOKEN'));
        $this->baseUrl = config('services.coingate.base_url', env('COINGATE_BASE_URL', 'https://api-sandbox.coingate.com/api/v2'));
    }

    public function createOrder(float $amountUsd, string $title, string $description, string $email): array
    {
        if ($amountUsd <= 0) {
            throw new Exception('Monto inválido.');
        }

        $payload = [
            'order_id'          => 'OTH-' . bin2hex(random_bytes(3)),
            'price_amount'      => number_format($amountUsd, 2, '.', ''),
            'price_currency'    => 'USD',
            'receive_currency'  => 'USDC',
            'title'             => $title,
            'description'       => $description,
            'purchaser_email'   => $email,
            'callback_url'      => url('/crypto/callback'),
            'success_url'       => url('/crypto/success'),
            'cancel_url'        => url('/crypto/cancel'),
            'shopper[type]'     => 'personal',
            'shopper[ip_address]' => request()->ip(),
            'shopper[email]'    => $email,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->token,
            'Content-Type'  => 'application/x-www-form-urlencoded; charset=utf-8',
        ])->asForm()->post("{$this->baseUrl}/orders", $payload);

        if (!$response->successful()) {
            return [
                'status' => 'error',
                'message' => 'Error al crear orden en CoinGate',
                'http_status' => $response->status(),
                'body' => $response->json(),
            ];
        }
        
        return $response->json();
    }
}
