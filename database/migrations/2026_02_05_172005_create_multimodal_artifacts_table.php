<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('multimodal_artifacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('simulation_sessions')->cascadeOnDelete();
            $table->string('type'); // ecg, xray, lab_result, audio_clip
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->json('metadata')->nullable(); // Clinical findings visible in the artifact
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multimodal_artifacts');
    }
};
