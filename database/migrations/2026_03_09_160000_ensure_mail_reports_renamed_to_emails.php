<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures mail_reports is renamed to emails (fixes case where previous migration
     * was marked run but rename did not persist). No data loss.
     */
    public function up(): void
    {
        if (!Schema::hasTable('mail_reports')) {
            return;
        }
        if (Schema::hasTable('emails')) {
            return;
        }

        Schema::rename('mail_reports', 'emails');

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql' && Schema::hasTable('emails')) {
            $this->renameSequenceIfExists('mail_reports_id_seq', 'emails_id_seq');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('emails') || Schema::hasTable('mail_reports')) {
            return;
        }
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            $this->renameSequenceIfExists('emails_id_seq', 'mail_reports_id_seq');
            try {
                DB::statement("ALTER TABLE emails ALTER COLUMN id SET DEFAULT nextval('mail_reports_id_seq')");
                DB::statement("ALTER SEQUENCE mail_reports_id_seq OWNED BY emails.id");
            } catch (\Throwable $e) {
                // ignore
            }
        }
        Schema::rename('emails', 'mail_reports');
    }

    /**
     * Rename sequence if it exists. Uses pg_class so it works on PostgreSQL < 10.
     */
    private function renameSequenceIfExists(string $oldName, string $newName): void
    {
        try {
            $exists = DB::selectOne(
                "SELECT 1 FROM pg_class c
                 JOIN pg_namespace n ON n.oid = c.relnamespace
                 WHERE n.nspname = 'public' AND c.relname = ? AND c.relkind = 'S'",
                [$oldName]
            );
            if (!$exists) {
                return;
            }
            DB::statement("ALTER SEQUENCE {$oldName} RENAME TO {$newName}");
            DB::statement("ALTER TABLE emails ALTER COLUMN id SET DEFAULT nextval('{$newName}')");
            DB::statement("ALTER SEQUENCE {$newName} OWNED BY emails.id");
        } catch (\Throwable $e) {
            // Sequence rename is optional; table rename already succeeded
        }
    }
};
