<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $isPatch = $this->method() === 'PATCH';

        return [
            'id_cart' => [
                $isPatch ? 'sometimes' : 'required',
                'exists:carts,id_cart',
                'integer'
            ],
            'id_product' => [
                $isPatch ? 'sometimes' : 'required',
                'exists:products,id_product',
                'integer'
            ],
            'quantity' => [
                $isPatch ? 'sometimes' : 'required',
                'integer',
                'min:1'
            ],
            'unit_price' => [
                $isPatch ? 'sometimes' : 'required',
                'decimal:0,2',
                'min:0'
            ],
            'subtotal' => [
                $isPatch ? 'sometimes' : 'required',
                'decimal:0,2',
                'min:0'
            ],
        ];
    }
}
