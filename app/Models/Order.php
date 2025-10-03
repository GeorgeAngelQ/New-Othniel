<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable =[
        'id_user',
        'date',
        'total',
        'status',
        'payment_method'
    ];
    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }
    public function OrderDetail(){
        return $this->hasMany(OrderDetail::class,'id_order');
    }
    public function payment(){
        return $this->hasOne(Payment::class,'id_order');
    }
}
