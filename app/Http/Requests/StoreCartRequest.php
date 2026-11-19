<?php

namespace App\Http\Requests;


class StoreCartRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'total' => 'required|decimal:2|min:0',
            'status' => 'required|string|max:255',
        ];
    }
}
