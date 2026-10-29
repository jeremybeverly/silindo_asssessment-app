<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version');
            // One statement per question; the candidate answers M or L (D-021).
            $table->text('statement');
            $table->string('language', 10)->nullable();
            $table->timestamps();

            $table->unique(['question_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_versions');
    }
};
