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
        Schema::create('server_mods', function (Blueprint $table) {
            $table->unsignedBigInteger('server_id');
            $table->unsignedBigInteger('mod_id');
            $table->string('mod_version', 50);
            $table->boolean('is_required')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['server_id', 'mod_id']);
            $table->foreign('server_id')->references('id_server')->on('servers')->onDelete('cascade');
            $table->foreign('mod_id')->references('id_mod')->on('mods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_mods');
    }
};
