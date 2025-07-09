<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->nullable()->constrained()->onDelete('set null'); // Post específico de un juego
            $table->string('title');
            $table->text('content');
            $table->json('images')->nullable(); // Array de URLs de imágenes subidas
            $table->enum('type', ['discussion', 'question', 'review', 'news'])->default('discussion');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('views')->default(0);
            $table->integer('replies_count')->default(0);
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();
            
            $table->index(['game_id', 'is_pinned', 'last_activity']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_posts');
    }
};