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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('name', 255);
            $table->string('login', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('email', 255)->unique();
            $table->string('uuid', 36)->unique();
            $table->boolean('is_online')->default(false);
            $table->json('cosmetics')->nullable();
            $table->enum('role', ['user', 'admin', 'moderator'])->default('user');
            $table->boolean('is_banned')->default(false);
            $table->datetime('banned_date_start')->nullable();
            $table->datetime('banned_date_end')->nullable();
            $table->decimal('money', 12)->default(0);
            $table->string('telegram_link', 255)->nullable()->unique();
            $table->string('discord_link', 255)->nullable()->unique();
            $table->text('profile_info')->nullable();
            $table->boolean('is_private')->default(false);
            $table->boolean('is_child')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->json('parent_settings')->nullable();
            $table->enum('who_can_message', ['all', 'friends', 'nobody'])->default('all');
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id_user')->on('users')->onDelete('set null');
            $table->index('login');
            $table->index('email');
            $table->index('uuid');
            $table->index('is_online');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
