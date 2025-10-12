<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_product';
    protected $fillable =[
        'name',
        'description',
        'price',
        'stock',
        'image_url'
    ];
    
    public function getRouteKeyName()
    {
        return 'id_product';
    }

    public function orderDetails(){
        return $this->hasMany(OrderDetail::class,'id_product','id_product');
    }
    public function cartDetails(){
        return $this->hasMany(CartDetail::class,'id_product','id_product');
    }
}
