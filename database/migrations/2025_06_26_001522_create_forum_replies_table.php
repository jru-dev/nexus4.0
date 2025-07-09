<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_reply_id')->nullable()->constrained('forum_replies')->onDelete('cascade'); // Para respuestas anidadas
            $table->text('content');
            $table->json('images')->nullable(); // Array de URLs de imágenes subidas
            $table->boolean('is_approved')->default(true);
            $table->integer('likes')->default(0);
            $table->timestamps();
            
            $table->index(['forum_post_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_replies');
    }
};
