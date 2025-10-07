<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_order';
    protected $fillable =[
        'id_user',
        'id_cart',
        'date',
        'total',
        'status',
        'payment_method'
    ];
    public function users(){
        return $this->belongsTo(User::class, 'id_user','id_user');
    }
    public function carts(){
        return $this->belongsTo(Cart::class,'id_cart','id_cart');
    }
    public function OrderDetails(){
        return $this->hasMany(OrderDetail::class,'id_order','id_order');
    }
    public function payments(){
        return $this->hasOne(Payment::class,'id_order','id_order');
    }
}
