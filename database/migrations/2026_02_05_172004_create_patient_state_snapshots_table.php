<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_state_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('simulation_sessions')->cascadeOnDelete();
            $table->foreignId('turn_id')->nullable()->constrained('conversation_turns')->cascadeOnDelete();
            $table->json('vitals')->nullable(); // HR, BP, RR, SpO2, Temp
            $table->json('symptoms')->nullable();
            $table->string('emotional_status')->nullable();
            $table->json('interventions_applied')->nullable(); // Meds given, actions taken
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_state_snapshots');
    }
};
