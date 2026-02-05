<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thought_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turn_id')->constrained('conversation_turns')->cascadeOnDelete();
            $table->longText('reasoning_trace')->nullable(); // The raw chain of thought
            $table->float('confidence')->nullable();
            $table->integer('branching_factor')->default(1);
            $table->json('tags')->nullable(); // e.g. ["diagnosis", "empathy_check", "safety_guardrail"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thought_signatures');
    }
};
