<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_libraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->timestamp('purchased_at');
            $table->decimal('purchase_price', 8, 2);
            $table->integer('hours_played')->default(0);
            $table->enum('status', ['not_played', 'playing', 'completed', 'abandoned'])->default('not_played');
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'game_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_libraries');
    }
};