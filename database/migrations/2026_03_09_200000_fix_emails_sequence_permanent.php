<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Permanently fix compose email duplicate key: use one sequence (emails_id_seq),
     * point emails.id default at it, and sync to MAX(id).
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql' || !Schema::hasTable('emails')) {
            return;
        }

        $maxId = (int) DB::table('emails')->max('id');
        $maxId = $maxId ?: 1;

        // 1) Ensure sequence exists and column default uses it
        $currentSeq = DB::selectOne("SELECT pg_get_serial_sequence('public.emails', 'id') AS seq");
        $currentSeqName = $currentSeq->seq ?? null;

        $emailsSeqExists = DB::selectOne(
            "SELECT 1 FROM pg_class c
             JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE n.nspname = 'public' AND c.relname = 'emails_id_seq' AND c.relkind = 'S'"
        );
        $mailReportsSeqExists = DB::selectOne(
            "SELECT 1 FROM pg_class c
             JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE n.nspname = 'public' AND c.relname = 'mail_reports_id_seq' AND c.relkind = 'S'"
        );

        if ($mailReportsSeqExists && !$emailsSeqExists) {
            DB::statement('ALTER SEQUENCE public.mail_reports_id_seq RENAME TO emails_id_seq');
        }
        if (!$emailsSeqExists && !$mailReportsSeqExists) {
            DB::statement("CREATE SEQUENCE public.emails_id_seq START WITH " . ($maxId + 1));
        }

        DB::statement("ALTER TABLE public.emails ALTER COLUMN id SET DEFAULT nextval('public.emails_id_seq')");
        DB::statement('ALTER SEQUENCE public.emails_id_seq OWNED BY public.emails.id');

        // 2) Sync sequence so next nextval() = MAX(id)+1
        DB::statement("SELECT setval('public.emails_id_seq', ?)", [$maxId]);

        // 3) Rename constraint if still old name
        try {
            DB::statement('ALTER TABLE public.emails RENAME CONSTRAINT mail_reports_pkey TO emails_pkey');
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        // Optional: revert constraint name only; sequence change left as-is
    }
};
