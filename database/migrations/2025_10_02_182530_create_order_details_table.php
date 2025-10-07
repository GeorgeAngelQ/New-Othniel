<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('id_order_detail');
            $table->foreignId('id_order')->constrained('orders','id_order')->onDelete('cascade');
            $table->foreignId('id_product')->constrained('products','id_product');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
