<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'id_cart' => 'required|integer|exists:carts,id_cart',
            'id_product' => 'required|integer|exists:products,id_product',
            'quantity' => 'required|integer|min:1'
        ];
    }
    public function messages(): array
    {
        return [
            'id_cart.required' => 'Cart ID is required.',
            'id_cart.integer' => 'Cart ID must be an integer.',
            'id_cart.exists' => 'Cart ID must exist in carts table.',
            'id_product.required' => 'Product ID is required.',
            'id_product.integer' => 'Product ID must be an integer.',
            'id_product.exists' => 'Product ID must exist in products table.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
