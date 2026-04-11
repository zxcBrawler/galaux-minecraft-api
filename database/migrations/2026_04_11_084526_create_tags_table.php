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
        Schema::create('tags', function (Blueprint $table) {
            $table->id('id_tag');
            $table->string('name')->unique(); // 'Мини-игры', 'Выживание' и т.д.
            $table->string('slug')->unique(); // 'mini-games', 'survival' для URL
            $table->timestamps();
        });
        Schema::create('server_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('servers', 'id_server')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags', 'id_tag')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
