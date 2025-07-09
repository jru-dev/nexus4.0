<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            $table->string('profile_image')->nullable()->after('role'); // Imagen de perfil
            $table->text('bio')->nullable()->after('profile_image'); // Biografía opcional
            $table->timestamp('email_verified_at')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_image', 'bio']);
        });
    }
};