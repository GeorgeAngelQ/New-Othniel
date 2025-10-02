<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;;

class User extends Model
{
    use HasFactory;
    protected $fillable = [

    ];
    public function cart(){
        return $this->hasMany(Cart::class);
    }
    public function order(){
        return $this->hasMany(Order::class);
    }
}
