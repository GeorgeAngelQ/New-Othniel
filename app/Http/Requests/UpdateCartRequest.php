<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        $cart = $this->route('cart');
        return $cart && $this->user()->id_user === $cart->id_user;
    }
    public function rules(): array
    {
        $id_cart = $this->route('cart');
        $isPatch = $this->method() === 'PATCH';
        return [
            'total' => [
                $isPatch ? 'sometimes' : 'required',
                'decimal:0,2',
                'min:0'
            ],
            'status' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:50'
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'id_user.exists' => 'The selected user does not exist.',
            'total.decimal' => 'The total must be a decimal number with up to 2 decimal places.',
            'total.min' => 'The total must be at least 0.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status may not be greater than 50 characters.',
        ];
    }
}
