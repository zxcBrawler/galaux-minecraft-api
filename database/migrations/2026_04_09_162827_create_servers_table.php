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
        Schema::create('servers', function (Blueprint $table) {
            $table->id('id_server');
            $table->string('name', 255);
            $table->string('ip', 45);
            $table->integer('player_count')->unsigned()->default(0);
            $table->boolean('is_online')->default(false);
            $table->boolean('is_official')->default(false);
            $table->string('version', 50)->default('1.0');
            $table->string('mc_version', 20);
            $table->timestamps();

            $table->index('is_online');
            $table->index('mc_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
