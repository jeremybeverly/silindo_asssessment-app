<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table): void {
            $table->id();

            // One attempt per invitation, enforced by the database (D-019).
            $table->foreignId('invitation_id')->unique()->constrained()->restrictOnDelete();

            $table->string('state', 20)->default('in_progress')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
