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
        Schema::create('server_members', function (Blueprint $table) {
            $table->id('id_member');
            $table->unsignedBigInteger('server_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['owner', 'admin', 'moderator', 'builder'])->default('moderator');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['server_id', 'user_id']);
            $table->foreign('server_id')->references('id_server')->on('servers')->onDelete('cascade');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_members');
    }
};
