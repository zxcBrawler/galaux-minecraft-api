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
        Schema::create('mods', function (Blueprint $table) {
            $table->id('id_mod');
            $table->string('name', 255)->unique();
            $table->string('mod_id', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon_url', 500)->nullable();
            $table->string('modrinth_id', 100)->nullable();
            $table->integer('curseforge_id')->nullable();
            $table->timestamps();

            $table->index('mod_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mods');
    }
};
