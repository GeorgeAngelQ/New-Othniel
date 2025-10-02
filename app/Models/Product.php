<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable =[

    ];
    public function orderDetail(){
        return $this->hasMany(OrderDetail::class);
    }
    public function cartDetail(){
        return $this->hasMany(CartDetail::class);
    }
}
