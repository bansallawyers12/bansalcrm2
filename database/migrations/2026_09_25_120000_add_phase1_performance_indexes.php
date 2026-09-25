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
     * Phase 1 performance indexes (see docs/POSTGRES_PERFORMANCE_ROLLOUT_PLAN.md).
     * Indexes only — no data or schema changes beyond indexes.
     */
    public function up(): void
    {
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
    }

    /**
     * @return list<array{table: string, name: string, postgres: string, columns: list<string>}>
     */
    private function definitions(): array
    {
        return [
            [
                'table' => 'notifications',
                'name' => 'notifications_receiver_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS notifications_receiver_created_idx ON notifications (receiver_id, created_at DESC)',
                'columns' => ['receiver_id', 'created_at'],
            ],
            [
                'table' => 'notifications',
                'name' => 'notifications_receiver_status_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS notifications_receiver_status_idx ON notifications (receiver_id, receiver_status)',
                'columns' => ['receiver_id', 'receiver_status'],
            ],
            [
                'table' => 'applications',
                'name' => 'applications_client_id_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS applications_client_id_idx ON applications (client_id)',
                'columns' => ['client_id'],
            ],
            [
                'table' => 'applications',
                'name' => 'applications_user_id_status_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS applications_user_id_status_idx ON applications (user_id, status)',
                'columns' => ['user_id', 'status'],
            ],
            [
                'table' => 'applications',
                'name' => 'applications_checklist_sheet_status_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS applications_checklist_sheet_status_idx ON applications (checklist_sheet_status)',
                'columns' => ['checklist_sheet_status'],
            ],
            [
                'table' => 'staff_login_logs',
                'name' => 'staff_login_logs_user_created_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS staff_login_logs_user_created_idx ON staff_login_logs (user_id, created_at DESC)',
                'columns' => ['user_id', 'created_at'],
            ],
            [
                'table' => 'staff_login_logs',
                'name' => 'staff_login_logs_created_at_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS staff_login_logs_created_at_idx ON staff_login_logs (created_at)',
                'columns' => ['created_at'],
            ],
            [
                'table' => 'client_phones',
                'name' => 'client_phones_client_id_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS client_phones_client_id_idx ON client_phones (client_id)',
                'columns' => ['client_id'],
            ],
            [
                'table' => 'client_phones',
                'name' => 'client_phones_phone_country_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS client_phones_phone_country_idx ON client_phones (client_phone, client_country_code)',
                'columns' => ['client_phone', 'client_country_code'],
            ],
            [
                'table' => 'invoices',
                'name' => 'invoices_client_id_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS invoices_client_id_idx ON invoices (client_id)',
                'columns' => ['client_id'],
            ],
            [
                'table' => 'invoice_details',
                'name' => 'invoice_details_invoice_id_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS invoice_details_invoice_id_idx ON invoice_details (invoice_id)',
                'columns' => ['invoice_id'],
            ],
            [
                'table' => 'account_client_receipts',
                'name' => 'account_client_receipts_client_id_idx',
                'postgres' => 'CREATE INDEX CONCURRENTLY IF NOT EXISTS account_client_receipts_client_id_idx ON account_client_receipts (client_id)',
                'columns' => ['client_id'],
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
