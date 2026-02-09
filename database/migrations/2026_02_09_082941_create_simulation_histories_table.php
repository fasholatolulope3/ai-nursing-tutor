<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simulation_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('scenario_title');
            $table->string('scenario_type')->default('Clinical'); // Clinical, Comm, etc.
            $table->string('difficulty')->default('Medium'); // Easy, Medium, Hard
            $table->string('score_or_outcome')->nullable(); // e.g., "85/100" or "Pass"
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_histories');
    }
};
