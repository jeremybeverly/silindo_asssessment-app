<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Question versions are append-only (D-024): model guards cover Eloquent,
     * and this trigger covers direct SQL. PostgreSQL is the target engine.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION question_versions_prevent_mutation()
            RETURNS trigger
            LANGUAGE plpgsql
            AS $$
            BEGIN
                RAISE EXCEPTION 'question_versions rows are immutable (D-024)';
            END;
            $$;

            CREATE TRIGGER question_versions_immutable
            BEFORE UPDATE OR DELETE ON question_versions
            FOR EACH ROW
            EXECUTE FUNCTION question_versions_prevent_mutation();
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS question_versions_immutable ON question_versions;
            DROP FUNCTION IF EXISTS question_versions_prevent_mutation();
        SQL);
    }
};
