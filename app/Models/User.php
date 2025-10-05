<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;;

class User extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];
    public function carts(){
        return $this->hasMany(Cart::class,'id_user','id_user');
    }
    public function orders(){
        return $this->hasMany(Order::class,'id_user','id_user');
    }
}
