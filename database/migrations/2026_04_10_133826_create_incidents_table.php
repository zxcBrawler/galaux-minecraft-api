<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\IncidentType;
use App\Enums\IncidentStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id('id_incident');

            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');

            $table->foreignId('server_id')->nullable()->constrained('servers', 'id_server')->onDelete('set null');

            $table->string('type')->default(IncidentType::OTHER->value);

            $table->string('status')->default(IncidentStatus::OPEN->value);

            $table->foreignId('moderator_id')->nullable()->constrained('users', 'id_user')->onDelete('set null');

            $table->timestamp('moderator_joined_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
