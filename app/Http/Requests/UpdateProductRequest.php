<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $id_product = $this->route('product');
        $isPatch = $this->method() === 'PATCH';
        return [
            'name' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:255'
            ],
            'description' => [
                $isPatch ? 'sometimes' : 'nullable',
                'string',
            ],
            'price' => [
                $isPatch ? 'sometimes' : 'required',
                'decimal:0,2',
                'min:0'
            ],
            'stock' => [
                $isPatch ? 'sometimes' : 'required',
                'integer',
                'min:0'
            ],
            'image_url' => [
                $isPatch ? 'sometimes' : 'nullable',
                'url',
                'max:255'
            ],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'price.required' => 'The price field is required.',
            'price.decimal' => 'The price must be a decimal with up to 2 decimal places.',
            'price.min' => 'The price must be at least 0.',
            'stock.required' => 'The stock field is required.',
            'stock.integer' => 'The stock must be an integer.',
            'stock.min' => 'The stock must be at least 0.',
            'image_url.url' => 'The image URL must be a valid URL.',
            'image_url.max' => 'The image URL may not be greater than 255 characters.',
        ];
    }
}
