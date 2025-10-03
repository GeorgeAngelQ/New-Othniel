<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable =[
        'id_order',
        'id_product',
        'quantity',
        'unit_price',
        'subtotal'
    ];
    public function order(){
        return $this->belongsTo(Order::class, 'id_order');
    }
    public function product(){
        return $this->belongsTo(Product::class, 'id_product');
    }
}
