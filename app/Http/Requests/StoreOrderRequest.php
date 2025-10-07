<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'id_user' => 'required|exists:users,id_user',
            'id_cart' => 'nullable|exists:carts,id_cart',
            'total' => 'required|decimal:2|min:0',
            'payment_method' => 'nullable|string|max:100'
        ];
    }
    public function messages(): array
    {
        return [
            'id_user.required' => 'User ID is required.',
            'id_user.exists' => 'User ID must exist in users table.',
            'id_cart.exists' => 'Cart ID must exist in carts table.',
            'total.required' => 'Total is required.',
            'total.decimal' => 'Total must be a decimal number with 2 decimal places.',
            'total.min' => 'Total must be at least 0.',
            'payment_method.string' => 'Payment method must be a string.',
            'payment_method.max' => 'Payment method may not be greater than 100 characters.',
        ];
    }
}
