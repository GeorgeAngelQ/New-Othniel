<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Base\BaseRequest;

class RegisterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => 'required|string|min:8'
        ];
    }
}
