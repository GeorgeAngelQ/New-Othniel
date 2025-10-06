<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'id_user' => 'required|exists:users,id_user',
            'total' => 'required|decimal:2|min:0',
            'status' => 'required|string|max:255',
        ];
    }
}
