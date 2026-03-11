<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fixes compose-email duplicate key error:
     * 1. Sync the emails.id sequence to MAX(id) so next insert gets a new id.
     * 2. Rename primary key constraint from mail_reports_pkey to emails_pkey.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql' || !Schema::hasTable('emails')) {
            return;
        }

        // 1. Sync sequence: set so next nextval() returns MAX(id)+1 (fixes duplicate key on insert)
        $seqName = DB::selectOne("SELECT pg_get_serial_sequence('public.emails', 'id') AS seq");
        if (!empty($seqName->seq)) {
            $seq = $seqName->seq;
            DB::statement("SELECT setval(?, COALESCE((SELECT MAX(id) FROM public.emails), 1))", [$seq]);
        }

        // 2. Rename constraint so errors reference emails_pkey instead of mail_reports_pkey
        try {
            $constraintExists = DB::selectOne(
                "SELECT 1 FROM pg_constraint
                 WHERE conname = 'mail_reports_pkey' AND conrelid = 'public.emails'::regclass"
            );
            if ($constraintExists) {
                DB::statement('ALTER TABLE public.emails RENAME CONSTRAINT mail_reports_pkey TO emails_pkey');
            }
        } catch (\Throwable $e) {
            // Constraint may already be renamed or not exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql' || !Schema::hasTable('emails')) {
            return;
        }
        try {
            $constraintExists = DB::selectOne(
                "SELECT 1 FROM pg_constraint
                 WHERE conname = 'emails_pkey' AND conrelid = 'public.emails'::regclass"
            );
            if ($constraintExists) {
                DB::statement('ALTER TABLE public.emails RENAME CONSTRAINT emails_pkey TO mail_reports_pkey');
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
