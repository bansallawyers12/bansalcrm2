<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renames mail_reports to emails using raw SQL and explicit public schema
     * so the rename runs in the same database/schema as your DB client.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'pgsql') {
            if (Schema::hasTable('mail_reports') && !Schema::hasTable('emails')) {
                Schema::rename('mail_reports', 'emails');
            }
            return;
        }

        $hasMailReports = DB::selectOne(
            "SELECT EXISTS (
                SELECT 1 FROM information_schema.tables
                WHERE table_schema = 'public' AND table_name = 'mail_reports'
            ) AS ok"
        );
        $hasEmails = DB::selectOne(
            "SELECT EXISTS (
                SELECT 1 FROM information_schema.tables
                WHERE table_schema = 'public' AND table_name = 'emails'
            ) AS ok"
        );

        if (!($hasMailReports->ok ?? false) || ($hasEmails->ok ?? false)) {
            return;
        }

        DB::statement('ALTER TABLE public.mail_reports RENAME TO emails');

        try {
            $seqExists = DB::selectOne(
                "SELECT 1 FROM pg_class c
                 JOIN pg_namespace n ON n.oid = c.relnamespace
                 WHERE n.nspname = 'public' AND c.relname = 'mail_reports_id_seq' AND c.relkind = 'S'"
            );
            if ($seqExists) {
                DB::statement('ALTER SEQUENCE public.mail_reports_id_seq RENAME TO emails_id_seq');
                DB::statement("ALTER TABLE public.emails ALTER COLUMN id SET DEFAULT nextval('public.emails_id_seq')");
                DB::statement('ALTER SEQUENCE public.emails_id_seq OWNED BY public.emails.id');
            }
        } catch (\Throwable $e) {
            // Sequence rename optional
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            if (Schema::hasTable('emails') && !Schema::hasTable('mail_reports')) {
                Schema::rename('emails', 'mail_reports');
            }
            return;
        }

        $hasEmails = DB::selectOne(
            "SELECT EXISTS (
                SELECT 1 FROM information_schema.tables
                WHERE table_schema = 'public' AND table_name = 'emails'
            ) AS ok"
        );
        if (!($hasEmails->ok ?? false)) {
            return;
        }

        try {
            $seqExists = DB::selectOne(
                "SELECT 1 FROM pg_class c
                 JOIN pg_namespace n ON n.oid = c.relnamespace
                 WHERE n.nspname = 'public' AND c.relname = 'emails_id_seq' AND c.relkind = 'S'"
            );
            if ($seqExists) {
                DB::statement('ALTER SEQUENCE public.emails_id_seq RENAME TO mail_reports_id_seq');
                DB::statement("ALTER TABLE public.emails ALTER COLUMN id SET DEFAULT nextval('public.mail_reports_id_seq')");
                DB::statement('ALTER SEQUENCE public.mail_reports_id_seq OWNED BY public.emails.id');
            }
        } catch (\Throwable $e) {
            // ignore
        }

        DB::statement('ALTER TABLE public.emails RENAME TO mail_reports');
    }
};
