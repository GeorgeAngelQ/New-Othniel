<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_cart_detail';
    protected $fillable =[
        'id_cart',
        'id_product',
        'quantity',
        'subtotal'
    ];
    public function carts(){
        return $this->belongsTo(Cart::class,'id_cart','id_cart');
    }
    public function products(){
        return $this->belongsTo(Product::class,'id_product','id_product');
    }
}
