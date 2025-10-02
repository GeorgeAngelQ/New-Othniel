<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_details', function (Blueprint $table) {
            $table->id('id_cart_detail');
            $table->foreignId('id_cart')->constrained('carts','id_cart');
            $table->foreignId('id_product')->constrained('products','id_product');
            $table->integer('quantity');
            $table->decimal('subtotal', 8, 2);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cart_details');
    }
};
