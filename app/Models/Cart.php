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
    public function user(){
        return $this->belongsTo(User::class,'id_user');
    }
    public function cartDetail(){
        return $this->hasMany(CartDetail::class,'id_cart');
    }
}
