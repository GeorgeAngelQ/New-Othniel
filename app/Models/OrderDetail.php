<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_order_detail';
    protected $fillable =[
        'id_order',
        'id_product',
        'quantity',
        'price',
        'subtotal'
    ];
    public function orders(){
        return $this->belongsTo(Order::class, 'id_order','id_order');
    }
    public function products(){
        return $this->belongsTo(Product::class, 'id_product','id_product');
    }
}
