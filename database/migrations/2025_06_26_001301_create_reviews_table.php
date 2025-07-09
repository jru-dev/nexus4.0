<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 estrellas
            $table->string('title');
            $table->text('content');
            $table->boolean('is_approved')->default(true);
            $table->integer('helpful_votes')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'game_id']); // Una reseña por usuario por juego
            $table->index(['game_id', 'is_approved']);
        });

        // Agregar constraint después de crear la tabla
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT rating_range CHECK (rating >= 1 AND rating <= 5)');
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};