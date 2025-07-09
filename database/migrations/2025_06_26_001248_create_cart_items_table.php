<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2); // Precio al momento de agregar al carrito
            $table->timestamps();
            
            $table->unique(['cart_id', 'game_id']); // Un juego por carrito
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};