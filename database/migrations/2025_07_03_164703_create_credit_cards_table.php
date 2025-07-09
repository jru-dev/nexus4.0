<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number', 19); // 1234 5678 9012 3456
            $table->string('cardholder_name');
            $table->string('expiry_date', 7); // MM/YYYY
            $table->string('cvv', 4);
            $table->decimal('balance', 10, 2)->default(1000.00); // S/ 1000.00 por defecto
            $table->decimal('credit_limit', 10, 2)->default(5000.00); // Límite de crédito
            $table->string('bank_name');
            $table->enum('card_type', ['Visa', 'Mastercard', 'American Express']);
            $table->string('card_color')->default('#1e40af'); // Color visual de la tarjeta
            $table->boolean('is_active')->default(true);
            $table->string('currency', 3)->default('PEN'); // Soles peruanos
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index('card_number');
            $table->index(['bank_name', 'is_active']);
            $table->unique('card_number'); // Número de tarjeta único
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};