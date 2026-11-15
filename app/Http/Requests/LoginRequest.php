<?php

namespace App\Http\Requests;

class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ];
    }
}
