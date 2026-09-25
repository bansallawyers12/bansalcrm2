<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Concurrent index builds cannot run inside a transaction.
     *
     * @var bool
     */
    public $withinTransaction = false;

    /**
     * Phase 2 sheet performance: normalized stage column + supporting indexes.
     */
    public function up(): void
    {
        if (Schema::hasTable('applications') && ! Schema::hasColumn('applications', 'stage_normalized')) {
            Schema::table('applications', function (Blueprint $table): void {
                $table->string('stage_normalized', 255)->nullable()->after('stage');
            });
        }

        if (Schema::hasTable('applications') && Schema::hasColumn('applications', 'stage_normalized')) {
            DB::table('applications')->whereNull('stage_normalized')->update([
                'stage_normalized' => DB::raw("NULLIF(LOWER(TRIM(stage)), '')"),
            ]);
        }

        foreach ($this->definitions() as $index) {
            $this->createIndex($index['table'], $index['name'], $index['postgres'], $index['columns']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_reverse($this->definitions()) as $index) {
            $this->dropIndex($index['table'], $index['name']);
        }

        if (Schema::hasTable('applications') && Schema::hasColumn('applications', 'stage_normalized')) {
            Schema::table('applications', function (Blueprint $table): void {
                $table->dropColumn('stage_normalized');
            });
        }
    }

    /**
     * @return list<array{table: string, name: string, postgres: string, columns: list<string>}>
     */
    private function definitions(): array
    {
        return [
            [
                'table' => 'applications',
                'name' => 'applications_status_stage_normalized_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS applications_status_stage_normalized_idx ON applications (status, stage_normalized)',
                'columns' => ['status', 'stage_normalized'],
            ],
            [
                'table' => 'applications',
                'name' => 'applications_discontinued_status_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS applications_discontinued_status_idx ON applications (status) WHERE status = 2',
                'columns' => ['status'],
            ],
            [
                'table' => 'applications',
                'name' => 'applications_refund_status_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS applications_refund_status_idx ON applications (status) WHERE status = 8',
                'columns' => ['status'],
            ],
            [
                'table' => 'applications',
                'name' => 'applications_checklist_status_stage_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS applications_checklist_status_stage_idx ON applications (checklist_sheet_status, stage_normalized)',
                'columns' => ['checklist_sheet_status', 'stage_normalized'],
            ],
            [
                'table' => 'admins',
                'name' => 'admins_active_office_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_active_office_idx ON admins (office_id) WHERE is_archived = 0 AND (is_deleted IS NULL OR is_deleted = 0)',
                'columns' => ['office_id'],
            ],
            [
                'table' => 'admins',
                'name' => 'admins_visaexpiry_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS admins_visaexpiry_idx ON admins (visaexpiry) WHERE visaexpiry IS NOT NULL',
                'columns' => ['visaexpiry'],
            ],
            [
                'table' => 'application_activities_logs',
                'name' => 'application_activities_logs_app_type_updated_idx',
                'postgres' => "CREATE INDEX CONCURRENTLY IF NOT EXISTS application_activities_logs_app_type_updated_idx ON application_activities_logs (app_id, type, updated_at DESC) WHERE type = 'sheet_comment'",
                'columns' => ['app_id', 'type', 'updated_at'],
            ],
            [
                'table' => 'application_reminders',
                'name' => 'application_reminders_app_type_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS application_reminders_app_type_idx ON application_reminders (application_id, type)',
                'columns' => ['application_id', 'type'],
            ],
            [
                'table' => 'account_client_receipts',
                'name' => 'account_client_receipts_client_app_receipt_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS account_client_receipts_client_app_receipt_idx ON account_client_receipts (client_id, application_id, receipt_type)',
                'columns' => ['client_id', 'application_id', 'receipt_type'],
            ],
            [
                'table' => 'client_service_takens',
                'name' => 'client_service_takens_client_id_id_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS client_service_takens_client_id_id_idx ON client_service_takens (client_id, id DESC)',
                'columns' => ['client_id', 'id'],
            ],
        ];
    }

    /**
     * @param  list<string>  $columns
     */
    private function createIndex(string $table, string $name, string $postgresSql, array $columns): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement($postgresSql);

            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $name): void {
            $tableBlueprint->index($columns, $name);
        });
    }

    private function dropIndex(string $table, string $name): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasIndex($table, $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX CONCURRENTLY IF EXISTS '.$name);

            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($name): void {
            $tableBlueprint->dropIndex($name);
        });
    }
};
