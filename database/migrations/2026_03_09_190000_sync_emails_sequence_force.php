<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Force-sync the emails.id sequence so next insert gets MAX(id)+1.
     * Uses single SQL statement so the correct sequence is updated (fixes duplicate key on compose email).
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql' || !Schema::hasTable('emails')) {
            return;
        }

        // Single statement: get sequence from table column default, set to MAX(id) so next nextval() = MAX(id)+1
        DB::statement("
            SELECT setval(
                pg_get_serial_sequence('public.emails', 'id')::regclass,
                COALESCE((SELECT MAX(id) FROM public.emails), 1)
            )
        ");

        // Also rename constraint if still old name (idempotent)
        try {
            DB::statement("
                ALTER TABLE public.emails
                RENAME CONSTRAINT mail_reports_pkey TO emails_pkey
            ");
        } catch (\Throwable $e) {
            // Ignore if already renamed or not exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse setval; constraint rename reverted in previous migration if needed
    }
};
