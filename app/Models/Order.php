<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable =[

    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function OrderDetail(){
        return $this->hasMany(OrderDetail::class);
    }
    public function payment(){
        return $this->hasOne(Payment::class);
    }
}
