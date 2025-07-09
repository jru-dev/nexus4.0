<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->string('developer');
            $table->string('publisher');
            $table->date('release_date');
            $table->string('image_url')->nullable();
            $table->json('screenshots')->nullable(); // Array de URLs de capturas
            $table->json('system_requirements')->nullable(); // Requisitos del sistema
            $table->enum('age_rating', ['E', 'E10+', 'T', 'M', 'AO'])->default('E');
            $table->boolean('is_active')->default(true);
            $table->integer('stock')->default(0);
            $table->foreignId('category_id')->constrained('game_categories')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['is_active', 'category_id']);
            $table->index('price');
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
};