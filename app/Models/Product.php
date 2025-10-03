<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[
        'name',
        'description',
        'price',
        'stock',
        'image_url'
    ];
    public function orderDetail(){
        return $this->hasMany(OrderDetail::class,'id_product');
    }
    public function cartDetail(){
        return $this->hasMany(CartDetail::class,'id_product');
    }
}
