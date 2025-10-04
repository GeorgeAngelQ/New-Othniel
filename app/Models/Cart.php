<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable =[
        'id_user',
        'total',
        'status'
    ];
    public function users(){
        return $this->belongsTo(User::class,'id_user','id_user');
    }
    public function cartDetails(){
        return $this->hasMany(CartDetail::class,'id_cart','id_cart');
    }
}
