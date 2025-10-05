<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_order' => $this->id_order,
            'id_user' => $this->id_user,
            'date' => $this->date,
            'total' => $this->total,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('users')),
            'order_details' => OrderDetailResource::collection($this->whenLoaded('orderDetails')),
        ];
    }
}
