<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Base\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email:rfc,dns|max:255',
            'password' => 'required|string|min:8'
        ];
    }
}
