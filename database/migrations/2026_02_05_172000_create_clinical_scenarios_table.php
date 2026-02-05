<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinical_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('objective')->comment('Structured learning objectives');
            $table->enum('complexity', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->json('initial_patient_state')->comment('Baseline vitals and condition');
            $table->json('medical_history')->nullable()->comment('Patient background info');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_scenarios');
    }
};
