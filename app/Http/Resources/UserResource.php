<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_user' => $this->id_user,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'carts' => CartResource::collection($this->whenLoaded('carts')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}
