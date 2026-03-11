<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renames mail_reports table to emails. No data loss - table rename only.
     */
    public function up(): void
    {
        if (Schema::hasTable('mail_reports') && !Schema::hasTable('emails')) {
            Schema::rename('mail_reports', 'emails');
        }

        // PostgreSQL: rename sequence so it matches new table name (optional but clean)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql' && Schema::hasTable('emails')) {
            $sequenceOld = 'mail_reports_id_seq';
            $sequenceNew = 'emails_id_seq';
            try {
                if ($this->sequenceExists($sequenceOld)) {
                    DB::statement("ALTER SEQUENCE {$sequenceOld} RENAME TO {$sequenceNew}");
                    DB::statement("ALTER TABLE emails ALTER COLUMN id SET DEFAULT nextval('{$sequenceNew}')");
                    DB::statement("ALTER SEQUENCE {$sequenceNew} OWNED BY emails.id");
                }
            } catch (\Throwable $e) {
                // Sequence rename is optional; table rename already succeeded
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('emails') && !Schema::hasTable('mail_reports')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'pgsql') {
                $sequenceNew = 'emails_id_seq';
                $sequenceOld = 'mail_reports_id_seq';
                try {
                    if ($this->sequenceExists($sequenceNew)) {
                        DB::statement("ALTER SEQUENCE {$sequenceNew} RENAME TO {$sequenceOld}");
                        DB::statement("ALTER TABLE emails ALTER COLUMN id SET DEFAULT nextval('{$sequenceOld}')");
                        DB::statement("ALTER SEQUENCE {$sequenceOld} OWNED BY emails.id");
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }
            Schema::rename('emails', 'mail_reports');
        }
    }

    private function sequenceExists(string $name): bool
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            return false;
        }
        $result = DB::selectOne("SELECT 1 FROM pg_sequences WHERE schemaname = 'public' AND sequencename = ?", [$name]);
        return (bool) $result;
    }
};
