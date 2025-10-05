<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_payment';
    protected $fillable =[
        'id_order',
        'payment_method',
        'reference_transaction',
        'amount',
        'status'
    ];
    public function orders(){
        return $this->belongsTo(Order::class, 'id_order','id_order');
    }
}
