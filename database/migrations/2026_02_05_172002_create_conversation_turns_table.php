<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversation_turns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('simulation_sessions')->cascadeOnDelete();
            $table->string('role'); // user, model, system
            $table->longText('content');
            $table->string('content_modality')->default('text'); // text, image, audio, mixed
            $table->integer('tokens_used')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_turns');
    }
};
