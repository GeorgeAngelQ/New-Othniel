<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_payment');
            $table->foreignId('id_order')->constrained('orders','id_order');
            $table->string('payment_method');
            $table->string('reference_transaction')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
