<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $id_user = $this->route('user');
        $isPatch = $this->method() === 'PATCH';
        return [
            'name' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:255'
            ],
            'email' => [
                $isPatch ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id_user),
            ],
            'password' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'min:8'
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}
