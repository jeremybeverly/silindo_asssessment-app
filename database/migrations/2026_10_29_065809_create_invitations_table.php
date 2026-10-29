<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table): void {
            $table->id();

            // Only the hash is stored; the raw token is shown once at creation (NFR-03).
            $table->string('token_hash', 64)->unique();

            // Candidate identity supplied by HR. Email is deliberately not unique:
            // HR may invite the same person again (D-027).
            $table->string('candidate_name', 150);
            $table->string('candidate_email', 254)->index();
            $table->string('candidate_phone', 30);
            $table->string('candidate_phone_normalized', 20)->index();

            // Position snapshot: the office owns the list, this app preserves history (D-027).
            $table->string('position_name', 150);
            $table->string('position_external_id', 100)->nullable();

            $table->timestamp('access_starts_at');
            $table->timestamp('access_ends_at')->index();
            $table->string('status', 20)->default('issued')->index();
            $table->timestamp('revoked_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
