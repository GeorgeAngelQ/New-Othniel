<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;

class MercadoPagoService
{
public function crearOrden($cart)
{
    try {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $items = $cart->cartDetails->map(function ($detail) {
            return [
                "id" => $detail->id_product,
                "title" => $detail->product->name ?? 'Producto sin nombre',
                "description" => $detail->product->description ?? '',
                "picture_url" => $detail->product->image_url ?? '',
                "quantity" => $detail->quantity,
                "currency_id" => "PEN",
                "unit_price" => floatval($detail->unit_price)
            ];
        })->toArray();

        $data = [
            "items" => $items,
            "back_urls" => [
                "success" => "https://tusitio.com/pagos/success",
                "failure" => "https://tusitio.com/pagos/failure",
                "pending" => "https://tusitio.com/pagos/pending"
            ],
            "auto_return" => "approved",
        ];

        $client = new PreferenceClient();
        $preference = $client->create($data); // 👈 Esto debe retornar un objeto válido

        return $preference; // ✅ Retorna el objeto, no un JSONResponse

    } catch (MPApiException $e) {
        return [
            'error' => true,
            'message' => 'Error al crear preferencia',
            'api_response' => $e->getApiResponse()
        ];
    }
}

}
