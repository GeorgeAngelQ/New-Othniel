<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class MercadoPagoService
{
    public function crearOrden(Order $order)
    {
        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

            $items = $order->orderDetails->map(function ($detail) {
                return [
                    "id" => $detail->id_product,
                    "title" => $detail->product->name ?? 'Producto sin nombre',
                    "description" => $detail->product->description ?? '',
                    "picture_url" => $detail->product->image_url ?? '',
                    "quantity" => $detail->quantity,
                    "currency_id" => "PEN",
                    "unit_price" => floatval($detail->price)
                ];
            })->toArray();

            $payer = [
                "name" => $order->user->name ?? 'Cliente',
                "email" => $order->user->email ?? 'test@example.com',
            ];

            $data = [
                "items" => $items,
                "payer" => $payer,
                "back_urls" => [
                    "success" => "https://mi-tunel.ngrok.io/pagos/success",
                    "failure" => "https://mi-tunel.ngrok.io/pagos/failure",
                    "pending" => "https://mi-tunel.ngrok.io/pagos/pending"
                ],
                "auto_return" => "approved",
                "external_reference" => $order->id_order,
                "notification_url" => "https://mi-tunel.ngrok.io/pagos/notification"
            ];

            $client = new PreferenceClient();
            $preference = $client->create($data);

            $order->update([
                'preference_id' => $preference->id,
                'status' => 'active'
            ]);

            return [
                'status' => 'success',
                'message' => 'Preferencia creada correctamente',
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
                'id_preference' => $preference->id,
            ];
        } catch (MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            Log::error('Error MercadoPago API:', [
                'message' => $e->getMessage(),
                'status' => $e->getCode(),
                'response' => $apiResponse,
                'body' => $e->getApiResponse() ? $e->getApiResponse()->getContent() : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error inesperado MercadoPago:', ['msg' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Error inesperado al crear la preferencia',
                'error' => $e->getMessage(),
            ];
        }
    }
}
