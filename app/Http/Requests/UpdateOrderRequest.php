<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $id_order = $this->route('order');
        $isPatch = $this->method() === 'PATCH';
        return [
            'id_user' => [
                $isPatch ? 'sometimes' : 'required',
                'exists:users,id_user',
                'integer'
            ],

            'date' => [
                $isPatch ? 'sometimes' : 'required',
                'date'
            ],
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
            'payment_method' => [
                $isPatch ? 'sometimes' : 'nullable',
                'string',
                'max:100'
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'id_cart.exists' => 'The selected cart does not exist.',
            'date.date' => 'The date is not a valid date.',
            'total.decimal' => 'The total must be a decimal number with up to 2 decimal places.',
            'total.min' => 'The total must be at least 0.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status may not be greater than 50 characters.',
            'payment_method.string' => 'The payment method must be a string.',
            'payment_method.max' => 'The payment method may not be greater than 100 characters.',
        ];
    }
}
