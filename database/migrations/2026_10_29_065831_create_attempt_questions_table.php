<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_version_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('position');
            $table->timestamps();

            // The randomized order is fixed when the attempt starts (D-020).
            $table->unique(['attempt_id', 'position']);
            $table->unique(['attempt_id', 'question_version_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_questions');
    }
};
